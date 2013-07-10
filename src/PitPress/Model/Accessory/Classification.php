<?php

//! @file Classification.php
//! @brief This file contains the Classification class.
//! @details
//! @author Filippo F. Fadda


namespace PitPress\Model\Accessory;


use ElephantOnCouch\Doc\Doc;


//! @brief This class is used to classify documents.
//! @details Every document belongs from one to five different categories, most commonly known as tags. A document that
//! must be classified is called item. Each time we store an item, we must save his associations with the related tags.
//! We can't save the tags' references in the item itself, because we cannot modify the item if one of the associated
//! tags has been deleted, so we must save a new document that stores the association between the item and his tag. To
//! create the relation we must create a so called classification document (an instance of the present class) for each
//! tag related to the item. Then, we must also create two different views (associated_tags, related_items), the first
//! one will emit as key the item's ID and the classification document as value, the second one, instead, will emit the
//! tag's ID as key and always the classification's document as value. It's important emit always the entire classification
//! because it's easy to query items by tag and query tags by item.<br />
//! When the user deletes a tag, we must delete every classification document related: we can accomplish this, querying
//! the 'related_items' view, using as key, the tag ID. When instead, the user deletes an item, we query the view
//! 'associated_tags', using as key the item ID, and we remove every classification document associated with that item ID.
//! Last but not least important, sometime happens the user change the item, removing an associated tag, or adding one.
//! In case this happens, we have to simply remove the classification document of which we know the ID.
//! @nosubgrouping
class Classification extends Doc {

  public function __construct($itemId, $tagId) {
    $this->meta["itemId"] = $itemId;
    $this->meta["tagId"] = $tagId;
    $this->meta["timestamp"] = time();
  }

}