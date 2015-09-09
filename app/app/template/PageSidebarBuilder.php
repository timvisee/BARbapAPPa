<?php

namespace app\template;

use app\language\LanguageManager;
use app\session\SessionManager;
use carbon\core\util\StringUtils;

// Prevent direct requests to this file due to security reasons
defined('APP_INIT') or die('Access denied!');

class PageSidebarBuilder {

    /**
     * Constructor.
     */
    public function __construct() { }

    /**
     * Alternate constructor.
     * This constructor allows method chaining.
     *
     * @param string|null $title [optional] The title, or null to use the default title.
     *
     * @return PageHeaderBuilder The instance.
     */
    public static function create($title = null) {
        return new self($title);
    }

    /**
     * Build and print the sidebar.
     */
    public function build() {
        ?>
        <div data-role="panel" id="main-panel" data-position="left" data-display="reveal" data-theme="a">
            <h3>
                <?php
                if(SessionManager::isLoggedIn())
                    echo __('general', 'welcome') . ' ' . SessionManager::getLoggedInUser()->getFullName();
                else
                    echo __('general', 'welcome');
                ?>
            </h3>

            <p>This is the sidebar menu panel.</p><br />
            <a href="#" data-rel="close" class="ui-btn ui-shadow ui-corner-all ui-btn-a ui-icon-delete ui-btn-icon-left ui-btn-inline">Close panel</a>
            <?php
            if(!StringUtils::equals(LanguageManager::getPreferredLanguage()->getTag(), 'nl-NL'))
                echo '<a href="language.php?lang_tag=nl-NL" class="ui-btn ui-shadow ui-corner-all ui-btn-a"><img src="style/image/flag/nl.png" /></a>';
            else
                echo '<a href="language.php?lang_tag=en-US" class="ui-btn ui-corner-all ui-shadow"><img src="style/image/flag/gb.png" /></a>';
            ?>
        </div>
        <?php
    }
}