<?php

namespace SilverStripe\SuperGlue;

use SilverStripe\Core\ClassInfo;
use SilverStripe\ORM\DataExtension;
use SilverStripe\ORM\ManyManyList;
use SilverStripe\ORM\DataObject;

class SubPageExtension extends DataExtension
{
    /**
     * @var array
     */
    private static $belongs_many_many = array(
        "SuperGluePages" => SiteTree::class,
    );

    /**
     * @inheritdoc
     */
    public function onAfterWrite()
    {
        $decorated = $this->getDecoratedBy("SilverStripe\\SuperGlue\\PageExtension");

        foreach ($decorated as $class) {
            $objects = call_user_func([$class, "get"]);

            foreach ($objects as $object) {
                if ($connector = $object->SuperGlueConnector) {
                    /** @var Connector $connector */
                    $connector = new $connector();

                    $list = $connector->getDataList($object);
                    $listIds = $list->column("ID");

                    if (in_array($this->owner->ID, $listIds)) {
                        /** @var ManyManyList $relationList */
                        $relationList = $object->SuperGlueSubPages();
                        $relationList->add($this->owner, array("SuperGlueSort" => $relationList->min("SuperGlueSort") - 1));
                    }
                }
            }
        }
    }

    /**
     * @param string $extension
     *
     * @return array
     */
    private function getDecoratedBy($extension)
    {
        $classes = array();

        foreach (ClassInfo::getValidSubClasses(DataObject::class) as $className) {
            if (singleton($className)->hasExtension($extension))) {
                $classes[] = $className;
            }
        }

        return $classes;
    }
}
