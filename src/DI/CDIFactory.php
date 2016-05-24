<?php

namespace Anax\DI;


class CDIFactory extends CDIFactoryDefault {

    public function __construct() {

        parent::__construct();

        $this->set('form', '\Mos\HTMLForm\CForm');

    }
}