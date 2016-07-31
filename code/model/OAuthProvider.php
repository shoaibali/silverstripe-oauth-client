<?php

// use League\OAuth2\Client\Provider\AbstractProvider;

/**
 * An OAuth Provider represents a collection oauth providers for authentication purposes.
 * These will be list of existing available providers enabled by developers.
 * See: https://github.com/thephpleague/oauth2-client/blob/master/README.PROVIDERS.md
 *
 * Multiple OAuth providesr can be enabled at the same time, to allow authenticating against
 * mutiple oauth providers such as Twitter, Facebook, LinkedIn etc. Depending on which providers
 * have been enabled and are active
 *
 * @package oauth-client
 *
 * @property string Title
 * @property string Active
 * @property string Active
 *
 * @method HasManyList Codes() List of PermissionRoleCode objects
 * @method ManyManyList Groups() List of Group objects
 */
class OAuthIdentityProvider extends DataObject {
    /**
     * @var array
     */
    private static $db = array(
        "Title" => "Varchar(512)",
        "Active" => "Boolean",
        "AuthorizationURL" => "Varchar(512)",
        "AccessTokenURL" => "Varchar(512)",
        "OwnerDetailsURL" => "Varchar(512)",
        "APIKey" => "Varchar",
        "APISecret" => "Varchar",
        "ClientID" => "Varchar",
        "Scope" => "Varchar",
        "RedirectURI" => "Varchar",
        "ProviderUniqueIdentifier" => "Varchar",
        "ConsumerUniqueIdentifier" => "Varchar"
    );

    private static $default_sort = '"Title"';

    private static $singular_name = 'OAuth Provider';

    private static $plural_name = 'OAuth Providers';

    function getCMSFields() {
        $fields = new FieldList();

        $fields->push( new TextField('Title', 'Name of provider', $this->Title));

        // TODO Add validation to ensure URLs are correct
        $fields->push( new TextField('AuthorizationURL', 'Base authorization url', $this->AuthorizationURL));
        $fields->push( new TextField('AccessTokenURL', 'Base access token url', $this->AccessTokenURL));
        $fields->push( new TextField('OwnerDetailsURL', 'Base owner details url', $this->OwnerDetailsURL));

        $fields->push( new TextField('RedirectURI',
                'RedirectURI - where user will be returned',
                // Director::absoluteBaseURL() . "/oauth/authorized"
                $this->RedirectURI
            )
        );

        $fields->push( new TextField('Scope', 'Scope of the app access to persons details', $this->Scope));
        $fields->push( new TextField('ProviderUniqueIdentifier',
                                'Name of key that is used to uniquely identify this user' .
                                ' at provider i.e uid, consumingId, email etc.',
                                $this->ProviderUniqueIdentifier
                            )
        );

        // TODO Add validation to see this field actually exists on Member's object within SilverStripe
        $fields->push( new TextField('ConsumerUniqueIdentifier',
                            "Name of key that Identity Provider's unique identifier " .
                            "will be mapped against SilverStripe Member e.g. Email, Username etc",
                            $this->ConsumerUniqueIdentifier
                        ),
            'ProviderUniqueIdentifier'
        );

        $fields->push( new TextField('APIKey', 'API key provided by provider', $this->APIKey));
        $fields->push( new PasswordField('APISecret', 'API secret provided by provider', $this->APISecret));
        $fields->push( new CheckBoxField('Active', 'Enable this provider', $this->Active));


        // $this->extend('updateCMSFields', $fields);

        return $fields;
    }
}
