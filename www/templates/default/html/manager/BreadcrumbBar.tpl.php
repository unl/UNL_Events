<nav id="breadcrumbs">
<!-- WDN: see glossary item 'breadcrumbs' -->
<h3 class="wdn_list_descriptor wdn-text-hidden">Breadcrumbs</h3>
                
<ul>
	<?php foreach ($context->crumbs as $text => $url): ?>
		<li>
			<?php if ($url != NULL): ?>
				<a href="<?php echo $url; ?>"><?php echo $text ?></a>
			<?php else: ?>
				<?php echo $text ?>
			<?php endif; ?>
		</li>
	<?php endforeach; ?>
</ul>
</nav>