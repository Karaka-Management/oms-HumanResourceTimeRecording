<?php
/**
 * Jingga
 *
 * PHP Version 8.1
 *
 * @package   Web\Timerecording
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.0
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

namespace Web\Timerecording;

use Modules\Organization\Models\Unit;
use Modules\Profile\Models\Profile;
use phpOMS\Uri\UriFactory;
use phpOMS\Views\View;

/**
 * Main view.
 *
 * @package Web\Timerecording
 * @license OMS License 2.0
 * @link    https://jingga.app
 * @since   1.0.0
 * @codeCoverageIgnore
 */
class TimerecordingView extends View
{
    /**
     * Navigation view
     *
     * @var View
     * @since 1.0.0
     */
    protected $nav = null;

    /**
     * User profile.
     *
     * @var Profile
     * @since 1.0.0
     */
    public $profile = null;

    /**
     * Organizations.
     *
     * @var Unit[]
     * @since 1.0.0
     */
    protected $organizations = null;

    /**
     * Set navigation view.
     *
     * @param View $nav Navigation view
     *
     * @return void
     *
     * @since 1.0.0
     * @codeCoverageIgnore
     */
    public function setNavigation(View $nav) : void
    {
        $this->nav = $nav;
    }

    /**
     * Get profile image
     *
     * @return string Profile image link
     *
     * @since 1.0.0
     */
    public function getProfileImage() : string
    {
        if ($this->profile === null || $this->profile->image->getPath() === '') {
            return UriFactory::build('Web/Timerecording/img/user_default_' . \mt_rand(1, 6) . '.png');
        }

        return UriFactory::build($this->profile->image->getPath());
    }

    /**
     * Set organizations
     *
     * @param Unit[] $organizations Organizations
     *
     * @return void
     *
     * @since 1.0.0
     * @codeCoverageIgnore
     */
    public function setOrganizations(array $organizations) : void
    {
        $this->organizations = $organizations;
    }
}
