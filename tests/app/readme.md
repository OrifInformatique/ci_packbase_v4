# liste test
- Test les accès
    - Autoriser d’accéder sans compte quand le contrôleur autorise sans compte
    (testAllUserAccessLevelWithoutAccount)
    - Autoriser d’accéder avec un compte registered quand le contrôleur
    autorise sans compte
    - Autoriser d’accéder avec un compte admin quand le contrôleur autorise
    sans compte

    - Interdiction d’accéder sans compte quand le contrôleur autorise avec un
    compte (testLoggedUserAccessLevelWithoutAccount)
    - Autoriser d’accéder avec un compte registered quand le contrôleur 
    autorise avec un compte (testLoggedUserAccessLevelWithRegistered)
    - Autoriser d’accéder avec un compte admin quand le contrôleur autorise
    avec un compte (testLoggedUserAccessLevelWithAdmin)

    - Interdiction d’accéder sans compte quand le contrôleur autorise avec un
    compte admin
    - Interdiction d’accéder avec un compte registered quand le contrôleur
    autorise avec un compte adminitrateur.
    - Autoriser d’accéder avec un compte admin quand le contrôleur autorise
    avec un compte admin

- display view affiche le header la view passée en string et le footer
- display view affiche le header les views passées en array et le footer

