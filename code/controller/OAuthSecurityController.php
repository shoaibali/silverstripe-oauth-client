<?php
/**
 * Controller for managing OAuth requests
 *
 * @package silverstripe-oauth-client
 * @author Shoaib Ali <shoaib@webstrike.co.nz>
 */
class OAuthSecurityController extends Controller {

    /**
     * This holds the settings array of oauth provider
     * provided details
     *
     * @var Array
     */
    private static $IdPsettings;

    /**
     * @var array
     */
    private static $allowed_actions = array(
        'index',
        'login',
    );

    public function init() {
        parent::init();

        // More identity providers can be added in future for oAuth (Facebook, Twitter etc).
        // Here we use the first active by default as there shouldn't be more than one active.
        $IdPsettings = OAuthIdentityProvider::get()->filter('Active', '1')->First();

        // When configured correctly this should never be the case. Just for security.
        if(empty($IdPsettings)) {
            // @TODO move to translation file.
            user_error(
                _t(
                    'NO_IDP_ACTIVE',
                    'No IdP Available or Active. Please define and activate an Identity Provder.'
                ),
                E_USER_ERROR
            );
        }
        // TODO How do we protect APISecret from not showing up in Error logs or stack trace raygun etc
        self::$IdPsettings = array(
            'clientId'                => $IdPsettings->APIKey,    // The client ID assigned to you by the provider
            'clientSecret'            => $IdPsettings->APISecret,   // The client password assigned to you by the provider
            'redirectUri'             => $IdPsettings->RedirectURI,
            'urlAuthorize'            => $IdPsettings->AuthorizationURL,
            'urlAccessToken'          => $IdPsettings->AccessTokenURL,
            'urlResourceOwnerDetails' => $IdPsettings->OwnerDetailsURL,
            'scopes' => $IdPsettings->Scope,
            'providerUniqueIdentifier' => $IdPsettings->ProviderUniqueIdentifier,
            'consumerUniqueIdentifier' => $IdPsettings->ConsumerUniqueIdentifier,
        );

        // Prevent clickjacking, see https://developer.mozilla.org/en-US/docs/HTTP/X-Frame-Options
        $this->response->addHeader('X-Frame-Options', 'SAMEORIGIN');
    }

    public function index() {
        return $this->redirect(Director::absoluteBaseURL() . 'Security/login');
    }


    /**
     * Log the current user into the identity provider, and then Silverstripe
     *
     */
    public function login() {

        $provider = new \League\OAuth2\Client\Provider\GenericProvider(self::$IdPsettings);

        // If we don't have an authorization code then get one
        if (empty($this->request->getVar('code'))) {

            // Fetch the authorization URL from the provider; this returns the
            // urlAuthorize option and generates and applies any necessary parameters
            // (e.g. state).
            $authorizationUrl = $provider->getAuthorizationUrl();

            // Get the state generated for you and store it to the session.
            Session::set('oauth2state', $provider->getState());
            Session::set_cookie_secure(true);


            // Redirect the user to the authorization URL.
            // TODO use silverstripe API to redirect $this->redirect();
            header('Location: ' . $authorizationUrl);
            exit();

        // Check given state against previously stored one to mitigate CSRF attack
        } elseif ($this->request->getVar('state') !== Session::get('oauth2state')) {

            Session::clear('oauth2state');
            // TODO raise user_error or redirect to get a new state
            exit('Invalid state');
        } else {

            try {

                // Try to get an access token using the authorization code grant.
                $accessToken = $provider->getAccessToken('authorization_code',
                    array('code' => $this->request->getVar('code'))
                );

                // We have an access token, which we may use in authenticated
                // requests against the service provider's API.
                // echo $accessToken->getToken() . "\n";
                // echo $accessToken->getRefreshToken() . "\n";
                // echo $accessToken->getExpires() . "\n";
                // echo ($accessToken->hasExpired() ? 'expired' : 'not expired') . "\n";

                // Using the access token, we may look up details about the
                // resource owner.
                $resourceOwner = $provider->getResourceOwner($accessToken);


                // The provider provides a way to get an authenticated API request for
                // the service, using the access token; it returns an object conforming
                // to Psr\Http\Message\RequestInterface.
                // $request = $provider->getAuthenticatedRequest(
                //     'GET',
                //     self::$IdPsettings['urlResourceOwnerDetails'],
                //     $accessToken
                // );


                $member = $this->authenticate($resourceOwner->toArray());

                if(!$member instanceof Member) {
                    user_error(_t(
                        'NOT_A_VALID_MEMBER',
                        '{class} does not return a valid Member',
                        array('class' => get_class($auth))
                    ));
                }

                $member->login();

                Session::clear('oauth2state');

                return $this->redirect(Session::get('BackURL'));

            } catch (\League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {

                // Failed to get the access token or user details.
                exit($e->getMessage());

            }

        }

    }

    /**
     * Authenticate the user using attributes returned from IdP
     *
     * @param $resourceOwner Array containing resourceOwner
     * @todo Handle user provisioning
     */
    private function authenticate($resourceOwner) {
        // load the config to get the mapping of the keys provided by the IdP into
        //  SilverStripe member-class fields
        $providerUID = self::$IdPsettings['providerUniqueIdentifier'];
        $consumerUID = self::$IdPsettings['consumerUniqueIdentifier'];

        // load the member based on the provided UID (usually the mail address)
        $member = Member::get()->filter($consumerUID, $resourceOwner[$providerUID])->first();

        /*
         * @Note: Currently a existing account within SilverStripe is required.
         * If you want to provision members on the fly we are doing so below
         */
        if (!$member) {
            // create a new member (student/teacher)
            $member = new Member();

            // don't update Username and Email
            $member->Username = $resourceOwner[$providerUID];
            $member->$consumerUID = $resourceOwner[$providerUID];
            $member->Activated = true;
            $member->write();

            // TODO use the BackURL for redirection if avaiable from redirect_uri from provider
            Session::set('BackURL', '/Security/login');
        }

        return $member;
    }


}
