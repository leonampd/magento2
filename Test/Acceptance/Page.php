<?php

trait Page
{
    public function getPageFromSession()
    {
        return $this->getSession()->getPage();
    }
}
