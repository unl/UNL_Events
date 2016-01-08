<div class="wdn-grid-set wdn-footer-links-local">
    <div class="bp960-wdn-col-two-thirds">
        <div class="wdn-footer-module">
            <span role="heading" class="wdn-footer-heading">About UNL Events</span>
            <?php
            if ($file = @file_get_contents(\UNL\UCBCN\Util::getWWWRoot() . '/tmp/iim-app-footer.html')) {
                echo $file;
            } else {
                echo file_get_contents('http://iim.unl.edu/iim-app-footer?format=partial');
            }
            ?>
        </div>
    </div>
    <div class="bp960-wdn-col-one-third">
        <div class="wdn-footer-module">
            <span role="heading" class="wdn-footer-heading">Related Links</span>
            <ul class="wdn-related-links-v1">
                <li><a href="http://wdn.unl.edu/">Web Developer Network</a></li>
                <li><a href="http://iim.unl.edu/">Internet and Interactive Media</a></li>
                <li><a href="http://ucomm.unl.edu/">University Communications</a></li>
                <li><a href="http://its.unl.edu/">Information Technology Services</a></li>
            </ul>
        </div>
    </div>
</div>