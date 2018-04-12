<?php
return <<<TPL
<img src="exampleImg/{$this->stringToFilename($fieldText)}" class="{$fieldClass}"/>
TPL;
?>