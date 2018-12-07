<nav id="breadcrumbs" <?php if (isset($context->search)) echo 'class="search"' ?>>
<!-- WDN: see glossary item 'breadcrumbs' -->
<h3 class="wdn_list_descriptor dcf-sr-only">Breadcrumbs</h3>

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

<?php if (isset($context->search)): ?>
<div id="toggle-search">
	<button id="show-search"><span class="wdn-icon-search" aria-hidden="true"></span><span class="dcf-sr-only">search</span></button>
</div>
<?php endif; ?>
</nav>
