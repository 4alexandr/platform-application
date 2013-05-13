<?php

namespace Acme\Bundle\TestsBundle\Pages\BAP;

use Acme\Bundle\TestsBundle\Pages\PageFilteredGrid;

class Groups extends PageFilteredGrid
{
    const URL = 'user/group';

    public function __construct($testCase, $redirect = true)
    {
        $this->redirectUrl = self::URL;
        parent::__construct($testCase, $redirect);

    }

    public function add()
    {
        $this->test->byXPath("//a[contains(., 'Add new')]")->click();
        $this->waitPageToLoad();
        $this->waitForAjax();
        return new Group($this->test);
    }
}
