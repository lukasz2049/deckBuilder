<?php
return <<<TPL
<div class="Card {$this->type} lang_{$this->lang}">
	Welcome to deckBuilder!<br>
	Edit "card.tpl" to change Card Template!<br>
	{$this->field('Title')}
</div>
TPL;
?>