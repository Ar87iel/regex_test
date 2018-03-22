<?php
/**
 * @category WebPT
 * @package EMRAdmin
 * @copyright Copyright (c) 2016 WebPT, INC
 * @author Tim Bradley (timothy.bradley@webpt.com)
 */

chdir(__DIR__);
echo shell_exec( __DIR__ . '/../../vendor/bin/generate-spec-html --public=../../public --spec=./spec --remote=../../public/shared' );
