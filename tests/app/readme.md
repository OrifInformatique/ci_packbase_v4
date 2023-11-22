# liste test
- Test les accès
    - Autoriser d’accéder sans compte quand le contrôleur autorise sans compte
    (`test_all_user_access_level_without_account`)
    - Autoriser d’accéder avec un compte registered quand le contrôleur
    autorise sans compte (`test_all_user_access_level_with_registered`)
    - Autoriser d’accéder avec un compte admin quand le contrôleur autorise
    sans compte (`test_all_user_access_level_with_registered`)

    - Interdiction d’accéder sans compte quand le contrôleur autorise avec un
    compte (`test_logged_user_access_level_without_account`)
    - Autoriser d’accéder avec un compte registered quand le contrôleur 
    autorise avec un compte (`test_logged_user_access_level_with_registered`)
    - Autoriser d’accéder avec un compte admin quand le contrôleur autorise
    avec un compte (`test_logged_user_access_level_with_admin`)

    - Interdiction d’accéder sans compte quand le contrôleur autorise avec un
    compte admin (`test_admin_access_level_without_account`)
    - Interdiction d’accéder avec un compte registered quand le contrôleur
    autorise avec un compte adminitrateur.
    (`test_admin_access_level_with_registered`)
    - Autoriser d’accéder avec un compte admin quand le contrôleur autorise
    avec un compte admin (`test_admin_access_level_with_admin`)

- `display_view` affiche le header, la view passée en string et le footer
(`test_display_view_with_view_by_string`)

- `display_view` affiche le header, les views passées en array et le footer
(`test_display_view_with_view_by_array`)

- Test le blocage de la page par iniController
(`test_display_view_when_unauthorized`)
- Test l’autorisation de la page par iniController
(`test_display_view_when_authorized`)

