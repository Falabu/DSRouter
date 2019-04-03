<?php

namespace DSRouter;

class AbstractController
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }
}