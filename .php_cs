<?php

// Needed to get styleci-bridge loaded
require_once './vendor/autoload.php';

use SLLH\StyleCIBridge\ConfigBridge;
use Symfony\CS\Fixer\Contrib\HeaderCommentFixer;

$header = <<<EOF
@author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
@copyright Copyright (c) Reiss Clothing Ltd.

For the full copyright and license information, please view the LICENSE
file that was distributed with this source code.
EOF;

HeaderCommentFixer::setHeader($header);

return ConfigBridge::create()
    ->setUsingCache(true)
;
