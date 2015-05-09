(function(root) {

    var bhIndex = null;
    var rootPath = '';
    var treeHtml = '    <ul>                <li data-name="namespace:" class="opened">                    <div style="padding-left:0px" class="hd">                        <span class="glyphicon glyphicon-play"></span><a href=".html">Bbatsche</a>                    </div>                    <div class="bd">                            <ul>                <li data-name="namespace:Bbatsche_Entrust" class="opened">                    <div style="padding-left:18px" class="hd">                        <span class="glyphicon glyphicon-play"></span><a href="Bbatsche/Entrust.html">Entrust</a>                    </div>                    <div class="bd">                            <ul>                <li data-name="namespace:Bbatsche_Entrust_Contracts" >                    <div style="padding-left:36px" class="hd">                        <span class="glyphicon glyphicon-play"></span><a href="Bbatsche/Entrust/Contracts.html">Contracts</a>                    </div>                    <div class="bd">                            <ul>                <li data-name="class:Bbatsche_Entrust_Contracts_EntrustPermissionInterface" >                    <div style="padding-left:62px" class="hd leaf">                        <a href="Bbatsche/Entrust/Contracts/EntrustPermissionInterface.html">EntrustPermissionInterface</a>                    </div>                </li>                            <li data-name="class:Bbatsche_Entrust_Contracts_EntrustRoleInterface" >                    <div style="padding-left:62px" class="hd leaf">                        <a href="Bbatsche/Entrust/Contracts/EntrustRoleInterface.html">EntrustRoleInterface</a>                    </div>                </li>                            <li data-name="class:Bbatsche_Entrust_Contracts_EntrustUserInterface" >                    <div style="padding-left:62px" class="hd leaf">                        <a href="Bbatsche/Entrust/Contracts/EntrustUserInterface.html">EntrustUserInterface</a>                    </div>                </li>                </ul></div>                </li>                            <li data-name="namespace:Bbatsche_Entrust_Traits" >                    <div style="padding-left:36px" class="hd">                        <span class="glyphicon glyphicon-play"></span><a href="Bbatsche/Entrust/Traits.html">Traits</a>                    </div>                    <div class="bd">                            <ul>                <li data-name="class:Bbatsche_Entrust_Traits_EntrustControllerTrait" >                    <div style="padding-left:62px" class="hd leaf">                        <a href="Bbatsche/Entrust/Traits/EntrustControllerTrait.html">EntrustControllerTrait</a>                    </div>                </li>                            <li data-name="class:Bbatsche_Entrust_Traits_EntrustPermissionTrait" >                    <div style="padding-left:62px" class="hd leaf">                        <a href="Bbatsche/Entrust/Traits/EntrustPermissionTrait.html">EntrustPermissionTrait</a>                    </div>                </li>                            <li data-name="class:Bbatsche_Entrust_Traits_EntrustRoleTrait" >                    <div style="padding-left:62px" class="hd leaf">                        <a href="Bbatsche/Entrust/Traits/EntrustRoleTrait.html">EntrustRoleTrait</a>                    </div>                </li>                            <li data-name="class:Bbatsche_Entrust_Traits_EntrustUserTrait" >                    <div style="padding-left:62px" class="hd leaf">                        <a href="Bbatsche/Entrust/Traits/EntrustUserTrait.html">EntrustUserTrait</a>                    </div>                </li>                </ul></div>                </li>                            <li data-name="class:Bbatsche_Entrust_Entrust" >                    <div style="padding-left:44px" class="hd leaf">                        <a href="Bbatsche/Entrust/Entrust.html">Entrust</a>                    </div>                </li>                            <li data-name="class:Bbatsche_Entrust_EntrustFacade" >                    <div style="padding-left:44px" class="hd leaf">                        <a href="Bbatsche/Entrust/EntrustFacade.html">EntrustFacade</a>                    </div>                </li>                            <li data-name="class:Bbatsche_Entrust_EntrustPermission" >                    <div style="padding-left:44px" class="hd leaf">                        <a href="Bbatsche/Entrust/EntrustPermission.html">EntrustPermission</a>                    </div>                </li>                            <li data-name="class:Bbatsche_Entrust_EntrustRole" >                    <div style="padding-left:44px" class="hd leaf">                        <a href="Bbatsche/Entrust/EntrustRole.html">EntrustRole</a>                    </div>                </li>                            <li data-name="class:Bbatsche_Entrust_EntrustServiceProvider" >                    <div style="padding-left:44px" class="hd leaf">                        <a href="Bbatsche/Entrust/EntrustServiceProvider.html">EntrustServiceProvider</a>                    </div>                </li>                            <li data-name="class:Bbatsche_Entrust_HasRole" >                    <div style="padding-left:44px" class="hd leaf">                        <a href="Bbatsche/Entrust/HasRole.html">HasRole</a>                    </div>                </li>                            <li data-name="class:Bbatsche_Entrust_MigrationCommand" >                    <div style="padding-left:44px" class="hd leaf">                        <a href="Bbatsche/Entrust/MigrationCommand.html">MigrationCommand</a>                    </div>                </li>                </ul></div>                </li>                </ul></div>                </li>                </ul>';

    var searchTypeClasses = {
        'Namespace': 'label-default',
        'Class': 'label-info',
        'Interface': 'label-primary',
        'Trait': 'label-success',
        'Method': 'label-danger',
        '_': 'label-warning'
    };

    var searchIndex = [
                    {"type": "Namespace", "link": "Bbatsche.html", "name": "Bbatsche", "doc": "Namespace Bbatsche"},{"type": "Namespace", "link": "Bbatsche/Entrust.html", "name": "Bbatsche\\Entrust", "doc": "Namespace Bbatsche\\Entrust"},
            
            {"type": "Class", "fromName": "Bbatsche\\Entrust", "fromLink": "Bbatsche/Entrust.html", "link": "Bbatsche/Entrust/Entrust.html", "name": "Bbatsche\\Entrust\\Entrust", "doc": "&quot;This class is the main entry point of entrust. Usually this the interaction\nwith this class will be done through the Entrust Facade&quot;"},
                                                        {"type": "Method", "fromName": "Bbatsche\\Entrust\\Entrust", "fromLink": "Bbatsche/Entrust/Entrust.html", "link": "Bbatsche/Entrust/Entrust.html#method___construct", "name": "Bbatsche\\Entrust\\Entrust::__construct", "doc": "&quot;Create a new confide instance.&quot;"},
                    {"type": "Method", "fromName": "Bbatsche\\Entrust\\Entrust", "fromLink": "Bbatsche/Entrust/Entrust.html", "link": "Bbatsche/Entrust/Entrust.html#method_hasRole", "name": "Bbatsche\\Entrust\\Entrust::hasRole", "doc": "&quot;Checks if the current user has a role by its name&quot;"},
                    {"type": "Method", "fromName": "Bbatsche\\Entrust\\Entrust", "fromLink": "Bbatsche/Entrust/Entrust.html", "link": "Bbatsche/Entrust/Entrust.html#method_can", "name": "Bbatsche\\Entrust\\Entrust::can", "doc": "&quot;Check if the current user has a permission by its name&quot;"},
                    {"type": "Method", "fromName": "Bbatsche\\Entrust\\Entrust", "fromLink": "Bbatsche/Entrust/Entrust.html", "link": "Bbatsche/Entrust/Entrust.html#method_user", "name": "Bbatsche\\Entrust\\Entrust::user", "doc": "&quot;Get the currently authenticated user or null.&quot;"},
                    {"type": "Method", "fromName": "Bbatsche\\Entrust\\Entrust", "fromLink": "Bbatsche/Entrust/Entrust.html", "link": "Bbatsche/Entrust/Entrust.html#method_routeNeedsRole", "name": "Bbatsche\\Entrust\\Entrust::routeNeedsRole", "doc": "&quot;Filters a route for a role or set of roles.&quot;"},
                    {"type": "Method", "fromName": "Bbatsche\\Entrust\\Entrust", "fromLink": "Bbatsche/Entrust/Entrust.html", "link": "Bbatsche/Entrust/Entrust.html#method_routeNeedsPermission", "name": "Bbatsche\\Entrust\\Entrust::routeNeedsPermission", "doc": "&quot;Filters a route for a permission or set of permissions.&quot;"},
                    {"type": "Method", "fromName": "Bbatsche\\Entrust\\Entrust", "fromLink": "Bbatsche/Entrust/Entrust.html", "link": "Bbatsche/Entrust/Entrust.html#method_routeNeedsRoleOrPermission", "name": "Bbatsche\\Entrust\\Entrust::routeNeedsRoleOrPermission", "doc": "&quot;Filters a route for role(s) and\/or permission(s).&quot;"},
            
            {"type": "Class", "fromName": "Bbatsche\\Entrust", "fromLink": "Bbatsche/Entrust.html", "link": "Bbatsche/Entrust/EntrustFacade.html", "name": "Bbatsche\\Entrust\\EntrustFacade", "doc": "&quot;\n&quot;"},
                    
            {"type": "Class", "fromName": "Bbatsche\\Entrust", "fromLink": "Bbatsche/Entrust.html", "link": "Bbatsche/Entrust/EntrustPermission.html", "name": "Bbatsche\\Entrust\\EntrustPermission", "doc": "&quot;\n&quot;"},
                                                        {"type": "Method", "fromName": "Bbatsche\\Entrust\\EntrustPermission", "fromLink": "Bbatsche/Entrust/EntrustPermission.html", "link": "Bbatsche/Entrust/EntrustPermission.html#method___construct", "name": "Bbatsche\\Entrust\\EntrustPermission::__construct", "doc": "&quot;Creates a new instance of the model.&quot;"},
                    {"type": "Method", "fromName": "Bbatsche\\Entrust\\EntrustPermission", "fromLink": "Bbatsche/Entrust/EntrustPermission.html", "link": "Bbatsche/Entrust/EntrustPermission.html#method_roles", "name": "Bbatsche\\Entrust\\EntrustPermission::roles", "doc": "&quot;Many-to-Many relations with role model.&quot;"},
                    {"type": "Method", "fromName": "Bbatsche\\Entrust\\EntrustPermission", "fromLink": "Bbatsche/Entrust/EntrustPermission.html", "link": "Bbatsche/Entrust/EntrustPermission.html#method_boot", "name": "Bbatsche\\Entrust\\EntrustPermission::boot", "doc": "&quot;Boot the permission model\nAttach event listener to remove the many-to-many records when trying to delete\nWill NOT delete any records if the permission model uses soft deletes.&quot;"},
            
            {"type": "Class", "fromName": "Bbatsche\\Entrust", "fromLink": "Bbatsche/Entrust.html", "link": "Bbatsche/Entrust/EntrustRole.html", "name": "Bbatsche\\Entrust\\EntrustRole", "doc": "&quot;\n&quot;"},
                                                        {"type": "Method", "fromName": "Bbatsche\\Entrust\\EntrustRole", "fromLink": "Bbatsche/Entrust/EntrustRole.html", "link": "Bbatsche/Entrust/EntrustRole.html#method___construct", "name": "Bbatsche\\Entrust\\EntrustRole::__construct", "doc": "&quot;Creates a new instance of the model.&quot;"},
                    {"type": "Method", "fromName": "Bbatsche\\Entrust\\EntrustRole", "fromLink": "Bbatsche/Entrust/EntrustRole.html", "link": "Bbatsche/Entrust/EntrustRole.html#method_users", "name": "Bbatsche\\Entrust\\EntrustRole::users", "doc": "&quot;Many-to-Many relations with the user model.&quot;"},
                    {"type": "Method", "fromName": "Bbatsche\\Entrust\\EntrustRole", "fromLink": "Bbatsche/Entrust/EntrustRole.html", "link": "Bbatsche/Entrust/EntrustRole.html#method_perms", "name": "Bbatsche\\Entrust\\EntrustRole::perms", "doc": "&quot;Many-to-Many relations with the permission model.&quot;"},
                    {"type": "Method", "fromName": "Bbatsche\\Entrust\\EntrustRole", "fromLink": "Bbatsche/Entrust/EntrustRole.html", "link": "Bbatsche/Entrust/EntrustRole.html#method_boot", "name": "Bbatsche\\Entrust\\EntrustRole::boot", "doc": "&quot;Boot the role model\nAttach event listener to remove the many-to-many records when trying to delete\nWill NOT delete any records if the role model uses soft deletes.&quot;"},
                    {"type": "Method", "fromName": "Bbatsche\\Entrust\\EntrustRole", "fromLink": "Bbatsche/Entrust/EntrustRole.html", "link": "Bbatsche/Entrust/EntrustRole.html#method_savePermissions", "name": "Bbatsche\\Entrust\\EntrustRole::savePermissions", "doc": "&quot;Save the inputted permissions.&quot;"},
                    {"type": "Method", "fromName": "Bbatsche\\Entrust\\EntrustRole", "fromLink": "Bbatsche/Entrust/EntrustRole.html", "link": "Bbatsche/Entrust/EntrustRole.html#method_attachPermission", "name": "Bbatsche\\Entrust\\EntrustRole::attachPermission", "doc": "&quot;Attach permission to current role.&quot;"},
                    {"type": "Method", "fromName": "Bbatsche\\Entrust\\EntrustRole", "fromLink": "Bbatsche/Entrust/EntrustRole.html", "link": "Bbatsche/Entrust/EntrustRole.html#method_detachPermission", "name": "Bbatsche\\Entrust\\EntrustRole::detachPermission", "doc": "&quot;Detach permission form current role.&quot;"},
                    {"type": "Method", "fromName": "Bbatsche\\Entrust\\EntrustRole", "fromLink": "Bbatsche/Entrust/EntrustRole.html", "link": "Bbatsche/Entrust/EntrustRole.html#method_attachPermissions", "name": "Bbatsche\\Entrust\\EntrustRole::attachPermissions", "doc": "&quot;Attach multiple permissions to current role.&quot;"},
                    {"type": "Method", "fromName": "Bbatsche\\Entrust\\EntrustRole", "fromLink": "Bbatsche/Entrust/EntrustRole.html", "link": "Bbatsche/Entrust/EntrustRole.html#method_detachPermissions", "name": "Bbatsche\\Entrust\\EntrustRole::detachPermissions", "doc": "&quot;Detach multiple permissions from current role&quot;"},
            
            {"type": "Class", "fromName": "Bbatsche\\Entrust", "fromLink": "Bbatsche/Entrust.html", "link": "Bbatsche/Entrust/EntrustServiceProvider.html", "name": "Bbatsche\\Entrust\\EntrustServiceProvider", "doc": "&quot;\n&quot;"},
                                                        {"type": "Method", "fromName": "Bbatsche\\Entrust\\EntrustServiceProvider", "fromLink": "Bbatsche/Entrust/EntrustServiceProvider.html", "link": "Bbatsche/Entrust/EntrustServiceProvider.html#method_boot", "name": "Bbatsche\\Entrust\\EntrustServiceProvider::boot", "doc": "&quot;Bootstrap the application events.&quot;"},
                    {"type": "Method", "fromName": "Bbatsche\\Entrust\\EntrustServiceProvider", "fromLink": "Bbatsche/Entrust/EntrustServiceProvider.html", "link": "Bbatsche/Entrust/EntrustServiceProvider.html#method_register", "name": "Bbatsche\\Entrust\\EntrustServiceProvider::register", "doc": "&quot;Register the service provider.&quot;"},
                    {"type": "Method", "fromName": "Bbatsche\\Entrust\\EntrustServiceProvider", "fromLink": "Bbatsche/Entrust/EntrustServiceProvider.html", "link": "Bbatsche/Entrust/EntrustServiceProvider.html#method_provides", "name": "Bbatsche\\Entrust\\EntrustServiceProvider::provides", "doc": "&quot;Get the services provided.&quot;"},
            
            {"type": "Trait", "fromName": "Bbatsche\\Entrust", "fromLink": "Bbatsche/Entrust.html", "link": "Bbatsche/Entrust/HasRole.html", "name": "Bbatsche\\Entrust\\HasRole", "doc": "&quot;\n&quot;"},
                                                        {"type": "Method", "fromName": "Bbatsche\\Entrust\\HasRole", "fromLink": "Bbatsche/Entrust/HasRole.html", "link": "Bbatsche/Entrust/HasRole.html#method_roles", "name": "Bbatsche\\Entrust\\HasRole::roles", "doc": "&quot;Many-to-Many relations with Role.&quot;"},
                    {"type": "Method", "fromName": "Bbatsche\\Entrust\\HasRole", "fromLink": "Bbatsche/Entrust/HasRole.html", "link": "Bbatsche/Entrust/HasRole.html#method_boot", "name": "Bbatsche\\Entrust\\HasRole::boot", "doc": "&quot;Boot the user model\nAttach event listener to remove the many-to-many records when trying to delete\nWill NOT delete any records if the user model uses soft deletes.&quot;"},
                    {"type": "Method", "fromName": "Bbatsche\\Entrust\\HasRole", "fromLink": "Bbatsche/Entrust/HasRole.html", "link": "Bbatsche/Entrust/HasRole.html#method_hasRole", "name": "Bbatsche\\Entrust\\HasRole::hasRole", "doc": "&quot;Checks if the user has a role by its name.&quot;"},
                    {"type": "Method", "fromName": "Bbatsche\\Entrust\\HasRole", "fromLink": "Bbatsche/Entrust/HasRole.html", "link": "Bbatsche/Entrust/HasRole.html#method_can", "name": "Bbatsche\\Entrust\\HasRole::can", "doc": "&quot;Check if user has a permission by its name.&quot;"},
                    {"type": "Method", "fromName": "Bbatsche\\Entrust\\HasRole", "fromLink": "Bbatsche/Entrust/HasRole.html", "link": "Bbatsche/Entrust/HasRole.html#method_ability", "name": "Bbatsche\\Entrust\\HasRole::ability", "doc": "&quot;Checks role(s) and permission(s).&quot;"},
                    {"type": "Method", "fromName": "Bbatsche\\Entrust\\HasRole", "fromLink": "Bbatsche/Entrust/HasRole.html", "link": "Bbatsche/Entrust/HasRole.html#method_attachRole", "name": "Bbatsche\\Entrust\\HasRole::attachRole", "doc": "&quot;Alias to eloquent many-to-many relation&#039;s attach() method.&quot;"},
                    {"type": "Method", "fromName": "Bbatsche\\Entrust\\HasRole", "fromLink": "Bbatsche/Entrust/HasRole.html", "link": "Bbatsche/Entrust/HasRole.html#method_detachRole", "name": "Bbatsche\\Entrust\\HasRole::detachRole", "doc": "&quot;Alias to eloquent many-to-many relation&#039;s detach() method.&quot;"},
                    {"type": "Method", "fromName": "Bbatsche\\Entrust\\HasRole", "fromLink": "Bbatsche/Entrust/HasRole.html", "link": "Bbatsche/Entrust/HasRole.html#method_attachRoles", "name": "Bbatsche\\Entrust\\HasRole::attachRoles", "doc": "&quot;Attach multiple roles to a user&quot;"},
                    {"type": "Method", "fromName": "Bbatsche\\Entrust\\HasRole", "fromLink": "Bbatsche/Entrust/HasRole.html", "link": "Bbatsche/Entrust/HasRole.html#method_detachRoles", "name": "Bbatsche\\Entrust\\HasRole::detachRoles", "doc": "&quot;Detach multiple roles from a user&quot;"},
            
            {"type": "Class", "fromName": "Bbatsche\\Entrust", "fromLink": "Bbatsche/Entrust.html", "link": "Bbatsche/Entrust/MigrationCommand.html", "name": "Bbatsche\\Entrust\\MigrationCommand", "doc": "&quot;\n&quot;"},
                                                        {"type": "Method", "fromName": "Bbatsche\\Entrust\\MigrationCommand", "fromLink": "Bbatsche/Entrust/MigrationCommand.html", "link": "Bbatsche/Entrust/MigrationCommand.html#method_fire", "name": "Bbatsche\\Entrust\\MigrationCommand::fire", "doc": "&quot;Execute the console command.&quot;"},
            
            
                                        // Fix trailing commas in the index
        {}
    ];

    /** Tokenizes strings by namespaces and functions */
    function tokenizer(term) {
        if (!term) {
            return [];
        }

        var tokens = [term];
        var meth = term.indexOf('::');

        // Split tokens into methods if "::" is found.
        if (meth > -1) {
            tokens.push(term.substr(meth + 2));
            term = term.substr(0, meth - 2);
        }

        // Split by namespace or fake namespace.
        if (term.indexOf('\\') > -1) {
            tokens = tokens.concat(term.split('\\'));
        } else if (term.indexOf('_') > 0) {
            tokens = tokens.concat(term.split('_'));
        }

        // Merge in splitting the string by case and return
        tokens = tokens.concat(term.match(/(([A-Z]?[^A-Z]*)|([a-z]?[^a-z]*))/g).slice(0,-1));

        return tokens;
    };

    root.Sami = {
        /**
         * Cleans the provided term. If no term is provided, then one is
         * grabbed from the query string "search" parameter.
         */
        cleanSearchTerm: function(term) {
            // Grab from the query string
            if (typeof term === 'undefined') {
                var name = 'search';
                var regex = new RegExp("[\\?&]" + name + "=([^&#]*)");
                var results = regex.exec(location.search);
                if (results === null) {
                    return null;
                }
                term = decodeURIComponent(results[1].replace(/\+/g, " "));
            }

            return term.replace(/<(?:.|\n)*?>/gm, '');
        },

        /** Searches through the index for a given term */
        search: function(term) {
            // Create a new search index if needed
            if (!bhIndex) {
                bhIndex = new Bloodhound({
                    limit: 500,
                    local: searchIndex,
                    datumTokenizer: function (d) {
                        return tokenizer(d.name);
                    },
                    queryTokenizer: Bloodhound.tokenizers.whitespace
                });
                bhIndex.initialize();
            }

            results = [];
            bhIndex.get(term, function(matches) {
                results = matches;
            });

            if (!rootPath) {
                return results;
            }

            // Fix the element links based on the current page depth.
            return $.map(results, function(ele) {
                if (ele.link.indexOf('..') > -1) {
                    return ele;
                }
                ele.link = rootPath + ele.link;
                if (ele.fromLink) {
                    ele.fromLink = rootPath + ele.fromLink;
                }
                return ele;
            });
        },

        /** Get a search class for a specific type */
        getSearchClass: function(type) {
            return searchTypeClasses[type] || searchTypeClasses['_'];
        },

        /** Add the left-nav tree to the site */
        injectApiTree: function(ele) {
            ele.html(treeHtml);
        }
    };

    $(function() {
        // Modify the HTML to work correctly based on the current depth
        rootPath = $('body').attr('data-root-path');
        treeHtml = treeHtml.replace(/href="/g, 'href="' + rootPath);
        Sami.injectApiTree($('#api-tree'));
    });

    return root.Sami;
})(window);

$(function() {

    // Enable the version switcher
    $('#version-switcher').change(function() {
        window.location = $(this).val()
    });

    
        // Toggle left-nav divs on click
        $('#api-tree .hd span').click(function() {
            $(this).parent().parent().toggleClass('opened');
        });

        // Expand the parent namespaces of the current page.
        var expected = $('body').attr('data-name');

        if (expected) {
            // Open the currently selected node and its parents.
            var container = $('#api-tree');
            var node = $('#api-tree li[data-name="' + expected + '"]');
            // Node might not be found when simulating namespaces
            if (node.length > 0) {
                node.addClass('active').addClass('opened');
                node.parents('li').addClass('opened');
                var scrollPos = node.offset().top - container.offset().top + container.scrollTop();
                // Position the item nearer to the top of the screen.
                scrollPos -= 200;
                container.scrollTop(scrollPos);
            }
        }

    
    
        var form = $('#search-form .typeahead');
        form.typeahead({
            hint: true,
            highlight: true,
            minLength: 1
        }, {
            name: 'search',
            displayKey: 'name',
            source: function (q, cb) {
                cb(Sami.search(q));
            }
        });

        // The selection is direct-linked when the user selects a suggestion.
        form.on('typeahead:selected', function(e, suggestion) {
            window.location = suggestion.link;
        });

        // The form is submitted when the user hits enter.
        form.keypress(function (e) {
            if (e.which == 13) {
                $('#search-form').submit();
                return true;
            }
        });

    
});


