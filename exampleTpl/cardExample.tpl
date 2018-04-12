<?php
return <<<TPL
<div class="Card {$this->type} fraction-{$this->field('Fraction', 'fieldSelector')} lang_{$this->lang}" id="">
	<div class="Box">
		<div class="Top">
			💰 {$this->field('Cost')}
			{$this->field('Title')}
		</div>
		<div class="Middle">
		{$this->field('Description')}
		{$this->field('Image', 'fieldImg')}
		</div>
		<div class="Bottom">
			<span class="BottomLeft">
			{$this->field('Attack Type')} {$this->field('Attack')}
			</span>
			<span class="BottomRight">
				🛡 {$this->field('Defense')}
			</span>
		</div>
	</div>
</div>
TPL;
?>