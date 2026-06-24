<?php

/**
 * @author Ohene Adjei
 */

namespace App\Concerns;

trait BuildsLoginRedirectUrl
{
    /**
     * Builds the current page's path + query string (with extra params
     * merged in) as a relative URL — never an absolute one. The "Login to
     * Order"/"Login to Save" links pass this as redirect_to so a guest
     * resumes here after logging in. It must stay relative because
     * FortifyServiceProvider only trusts a same-site relative path before
     * storing it as the post-login redirect — an absolute URL would be
     * silently rejected there, even for our own pages.
     */
    private function buildLoginRedirectUrl(array $extraQuery): string
    {
        $query = array_merge(request()->query(), $extraQuery);

        return '/'.request()->path().'?'.http_build_query($query);
    }
}
