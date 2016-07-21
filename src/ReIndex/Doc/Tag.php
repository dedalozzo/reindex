<?php

/**
 * @file Tag.php
 * @brief This file contains the Tag class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Doc;


use ReIndex\Property;
use ReIndex\Collection;
use ReIndex\Helper;
use Reindex\Exception;
use ReIndex\Task\SynonymizeTask;
use ReIndex\Enum\State;
use ReIndex\Security\Role;

use EoC\Opt\ViewQueryOpts;

use Phalcon\Di;


/**
 * @brief A label used to categorize posts.
 * @details Every post must be tagged with a maximun of five tags.
 * @nosubgrouping
 */
final class Tag extends Versionable {
  use Property\TExcerpt, Property\TBody, Property\TDescription;

  /** @name Constants */
  //!@{

  const SYNONYMIZING = "synonymizing"; //!< The tag has been marked as synonym.

  //!@}

  private $synonyms; // Collection of synonyms.


  public function __construct() {
    parent::__construct();

    $this->synonyms = new Collection\SynonymCollection('synonyms', $this->meta);
  }


  /**
   * @brief Given a list of IDs, returns the correspondent objects.
   * @retval array
   */
  public static function collect(array $ids) {
    if (empty($ids))
      return [];

    $couch = Di::getDefault()['couchdb'];

    $opts = new ViewQueryOpts();

    // Gets the tags properties.
    $opts->doNotReduce();
    $rows = $couch->queryView("tags", "all", $ids, $opts);

    Helper\ArrayHelper::unversion($ids);

    // Retrieves the number of posts per tag.
    $opts->reset();
    $opts->groupResults()->includeMissingKeys();
    $postsCount = $couch->queryView("posts", "perTag", $ids, $opts);

    $tags = [];
    $tagsCount = count($rows);
    for ($i = 0; $i < $tagsCount; $i++) {
      $tag = new \stdClass();
      $tag->id = $rows[$i]['id'];
      $tag->name = $rows[$i]['value'][0];
      $tag->excerpt = $rows[$i]['value'][1];
      $tag->createdAt = $rows[$i]['value'][2];
      //$entry->whenHasBeenPublished = Helper\Time::when($tags[$i]['value'][2]);
      $tag->postsCount = is_null($postsCount[$i]['value']) ? 0 : $postsCount[$i]['value'];

      $tags[] = $tag;
    }

    return $tags;
  }


  /** @name Synonyms Management Methods */
  //!@{

  /**
   * @brief Transforms the current tag into a synonym.
   * @param[in] Tag $tag The master tag.
   */
  public function markAsSynonymOf(Tag $tag) {
    if (!$this->state->is(State::CURRENT))
      throw new Exception\InvalidStateException('Only the current version of a tag can be synonymized.');

    if ($this->state->is(Tag::SYNONYMIZING))
      throw new Exception\InvalidStateException('The tag has been already marked as synonym.');

    $this->state->set(Tag::SYNONYMIZING);
    $this->save();

    $queue = $this->di['taskqueue'];
    $queue->add(new SynonymizeTask($tag->unversionId, $this->unversionId));
  }


  /**
   * @brief Returns a synonym having the (unversioned) ID used for the tag and even the same name.
   * @retval Synonym
   */
  public function castAsSynonym() {
    $synonym = Synonym::create($this->unversionId);
    $synonym->rev = $this->rev;
    $synonym->name = $this->name;
    $synonym->setRelatesIds($this->meta['synonyms']);
    return $synonym;
  }

  //!@}


  /**
   * @copydoc Versionable::approve()
   */
  public function approve() {
    if (!$value = $this->user->has(new Role\MemberRole\ApproveRevisionPermission($this)))
      throw new Exception\NotEnoughPrivilegesException("Privilegi insufficienti o stato incompatibile.");

    if ($this->user instanceof Member)
      $this->votes->cast($value, FALSE);

    if ($this->user instanceof System || $this->votes->count(FALSE) >= $this->di['config']->review->scoreToApproveRevision) {
      // Sets the state of the current revision (if any) to `approved`.
      $opts = new ViewQueryOpts();
      $opts->doNotReduce()->setKey($this->unversionId);
      $rows = $this->couch->queryView("tags", "unversion", NULL, $opts);

      if (!$rows->isEmpty()) {
        $current = $this->couch->getDoc(Couch::STD_DOC_PATH, $rows[0]['id']);
        $current->state->set(State::APPROVED);
        $current->save();
      }

      // This revision becomes the current one.
      $this->state->set(State::CURRENT);

      if (isset($this->body)) {
        $this->html = $this->markdown->parse($this->body);
        $purged = Helper\Text::purge($this->html);
        $this->excerpt = Helper\Text::truncate($purged);
      }

      $this->save();
    }
  }


  /**
   * @copydoc Versionable::delete()
   */
  public function delete() {
    parent::delete();

    $this->state->set(State::DELETED);

    $this->save();
  }


  //! @cond HIDDEN_SYMBOLS

  public function getName() {
    return $this->meta['name'];
  }


  public function issetName() {
    return isset($this->meta['name']);
  }


  public function setName($value) {
    // A tag name can't be changed unless the tag has never been approved.
    if ($this->state->is(State::CREATED))
      $this->meta['name'] = $value;
    else
      throw new \RuntimeException("Il nome di un tag non può essere cambiato.");
  }


  public function unsetName() {
    if ($this->isMetadataPresent('name'))
      unset($this->meta['name']);
  }


  public function getSynonyms() {
    return $this->synonyms;
  }


  public function issetSynonyms() {
    return isset($this->synonyms);
  }

  //! @endcond

}