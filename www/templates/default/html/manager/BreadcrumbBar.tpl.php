<?php if (isset($context->search)): ?>
<div id="toggle-search" title="Search">
	<a class="wdn-icon-search"></a>
</div>
<?php endif; ?>

<nav id="breadcrumbs" <?php if (isset($context->search)) echo 'class="search"' ?>>
<!-- WDN: see glossary item 'breadcrumbs' -->
<h3 class="wdn_list_descriptor wdn-text-hidden">Breadcrumbs</h3>

<ul>
	<?php foreach ($context->crumbs as $text => $url): ?>
		<li>
			<?php if ($url != NULL): ?>
				<a href="<?php echo $url; ?>"><?php echo $text ?></a>
			<?php else: ?>
				<a><?php echo $text ?></a>
			<?php endif; ?>
		</li>
	<?php endforeach; ?>
</ul>
</nav>
