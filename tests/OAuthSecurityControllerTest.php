<?php
/**
 * @package silverstripe-oauth-client
 * @subpackage tests
 */

class OAuthSecurityControllerTest extends FunctionalTest {


    /**
     * Test login using oAuth
     */
    public function testLoginRedirect() {
        $page = $this->get('oauth/login');

        // Home page should load..
        $this->assertEquals(302, $page->getStatusCode());

        // We should get redirected to authorization url

    }

    //@TODO add more tests!

}
