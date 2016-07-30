<?php
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
class OAuthProvider extends DataObject {
    /**
     * @var array
     */
    private static $db = array(
        "Title" => "Varchar(512)",
        "Active" => "Boolean",
    );

    private static $default_sort = '"Title"';

    private static $singular_name = 'OAuth Provder';

    private static $plural_name = 'OAuth Providers';

}
