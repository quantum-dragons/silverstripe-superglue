<?php

namespace SilverStripe\SuperGlue;

use SilverStripe\GraphQL\Controller;
use SilverStripe\ORM\DataObject;
use SilverStripe\Core\Extension;
use SilverStripe\ORM\PaginatedList;
use SilverStripe\Control\HTTPResponse;

class PageControllerExtension extends Extension
{
    /**
     * @var array
     */
    private static $allowed_actions = array(
        "LoadMore",
    );

    /**
     * @return HTTPResponse
     */
    public function LoadMore()
    {
        /** @var PaginatedList|DataObject[] $pages */
        $pages = $this->owner->SuperGlueViewSubPages();

        $connector = $this->owner->SuperGlueConnector;

        /** @var Connector $connector */
        $connector = new $connector();

        $items = array();

        foreach ($pages as $page) {
            if (method_exists($connector, "getPageArray")) {
                $item = $connector->getPageArray($page);
            } else {
                $item = $page->toMap();
            }

            $items[] = $item;
        }

        $data = array(
            "total" => (int) $pages->TotalItems(),
            "limit" => (int) $pages->getPageLength(),
            "start" => (int) $pages->getPageStart(),
            "next" => $pages->NextLink(),
            "items" => $items,
        );

        if ((int) $pages->getPageStart() >= (int) $pages->TotalItems() - (int) $pages->getPageLength()) {
            unset($data["next"]);
        }

        $response = new HTTPResponse();
        $response->setBody(json_encode($data));
        $response->addHeader("Content-type", "application/json");

        return $response;
    }
}
