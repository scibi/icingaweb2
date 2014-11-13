<?php
// {{{ICINGA_LICENSE_HEADER}}}
// {{{ICINGA_LICENSE_HEADER}}}

namespace Icinga\Module\Setup\Form;

use Icinga\Web\Form;

/**
 * Wizard page that displays a summary of what is going to be "done"
 */
class SummaryPage extends Form
{
    /**
     * The title of what is being set up
     *
     * @var string
     */
    protected $title;

    /**
     * The summary to show
     *
     * @var array
     */
    protected $summary;

    /**
     * Initialize this page
     */
    public function init()
    {
        $this->setName('setup_summary');
        $this->setViewScript('form/setup-summary.phtml');
    }

    /**
     * Set the title of what is being set up
     *
     * @param   string  $title
     */
    public function setSubjectTitle($title)
    {
        $this->title = $title;
    }

    /**
     * Return the title of what is being set up
     *
     * @return  string
     */
    public function getSubjectTitle()
    {
        return $this->title;
    }

    /**
     * Set the summary to show
     *
     * @param   array   $summary
     *
     * @return  self
     */
    public function setSummary(array $summary)
    {
        $this->summary = $summary;
        return $this;
    }

    /**
     * Return the summary to show
     *
     * @return  array
     */
    public function getSummary()
    {
        return $this->summary;
    }
}