<?php

namespace Pantheon\Terminus\Commands\Site;

use Consolidation\OutputFormatters\StructuredData\RowsOfFields;

use Pantheon\Terminus\Commands\TerminusCommand;
use Terminus\Collections\Sites;
use Terminus\Models\Environment;

/**
 * Class InfoCommand
 * @package Pantheon\Terminus\Commands\Site
 */
class InfoCommand extends TerminusCommand
{
    /**
     * Retrieve connection info for a specific environment such as git, sftp, mysql, redis
     *
     * @authorized
     *
     * @command site:info
     *
     * @param string $site Name of the site to retrieve
     *
     * @return RowsOfFields
     *
     */
    public function siteInfo(
        $site,
        $options = ['format' => 'table', 'fields' => 'param,value']
    ) {
        $sites = new Sites();
        $site = $sites->get($site);
        $site_info  = $this->siteParams($site);
        return new RowsOfFields($site_info);
    }


    /**
     * Retrieve Environment#connectionInfo() in a structure suitable for formatting
     *   Ex: ['env' => 'live', 'param' => 'mysql_host', 'value' => 'onebox']
     *
     * @param Site $site A Terminus\Models\Environment to interrogate
     * @param string $filter An optional parameter name to filter results
     *
     * @return array of connection info parameters
     */
    protected function siteParams($site, $filter = null)
    {
        $params = [];
        $site_info   = $site->info();
        foreach ($site_info as $param => $value) {
            if (is_null($filter) or $param == $filter) {
                // temporary hack - upstream is an array
                if ($param == 'upstream') {
                    continue;
                }
                $params[] = array(
                    'site'   => $site_info['name'],
                    'param' => $param,
                    'value' => $value,
                );
            }
        }

        return $params;
    }
}
