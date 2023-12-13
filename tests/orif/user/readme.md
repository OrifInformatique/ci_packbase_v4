# Liste de test
- La connexion avec un compte local fonctionne quand on met des identifiants
valides. (`testloginPagePostedWithoutSessionWithUsernameAndPassword`)
- La connexion avec un compte local ne fonctionne pas quand on met des
identifiants invalides.
(`testloginPagePostedWithoutSessionWithUsernameAndIncorrectPassword`)

- L’ajout d’un nouvel utilisateur fonctionne quand on est connecté avec un
compte administrateur. (`testsave_userWithUserId`)
- L’ajout d’un nouvel utilisateur ne fonctionne pas quand on n’est pas connecté
avec un compte administrateur.
- L’édition d’un utilisateur fonctionne quand on est connecté avec un compte
administrateur. (`testsave_userWithUserIdWithSameSessionUserId`)
- L’édition d’un utilisateur ne fonctionne pas quand on n’est pas connecté avec
un compte administrateur.
- La suppression d’un utilisateur fonctionne quand on est connecté avec un
compte administrateur. (`testdelete_userWitDeleteAction`)
- La suppression d’un utilisateur ne fonctionne pas quand on n’est pas connecté
avec un compte administrateur. (`testdelete_userWithoutSession`)
- La désactivation d’un utilisateur fonctionne quand on est connecté avec un
compte administrateur. (`testdelete_userWitDisableAction`)
- La désactivation d’un utilisateur ne fonctionne pas quand on n’est pas
connecté avec un compte administrateur.
- La réactivation d’un utilisateur fonctionne quand on est connecté avec un
compte administrateur. (`testreactivate_userWithExistingUser`)
- La réactivation d’un utilisateur ne fonctionne pas quand on n’est pas
connecté avec un compte administrateur.
 
- Mettre un utilisateur en administrateur fonctionne quand on est connecté avec
un compte administrateur. (`testsave_userWithUserId`)
- Mettre un utilisateur en administrateur ne fonctionne pas quand on n’est pas
connecté avec un compte administrateur.
- L'affichage de la liste des utilisateurs fonctionne quand on est connecté
avec un compte administrateur. (`testlist_userWithAdministratorSession`)
- L'affichage de la liste des utilisateurs ne fonctionne pas quand on n’est pas
connecté avec un compte administrateur.
 
- Modifier le mot de passe fonctionne quand on est connecté à un compte.
(`testpassword_change_user`)
- Modifier le mot de passe ne fonctionne pas quand on n’est pas connecté à un
compte. 
- Modifier le mot de passe ne fonctionne pas quand on ne met pas deux fois le
  même mot de passe.
  (`testpassword_change_userPostedWhenChangingPasswordWithError`)

- Se déconnecter (`testlogout`)



## Azure
- La création du compte avec azure fonctionne.
    - Envoyer de mail (non testable en l’état)
    - Page de saisi de code reçu par mail, Validation avec le code
        - Première tentative de code (`test_azure_mail_without_code`)
        - Échoue à mettre un code correct (`test_azure_mail_with_fake_code`)
        - Échoue 3x à mettre un code correct
        (`test_azure_mail_with_fake_code_all_attemps_done`)
        - Réussi à mettre un code correct
        (`test_azure_mail_with_correct_code_existing_user`)
    - Création du compte avec le bon nom d’utilisateur
    (`test_azure_mail_with_correct_code_new_user`)
- La connexion avec un compte azure fonctionne quand on met des identifiants
valides. (non testable en l’état)
- La connexion avec un compte azure ne fonctionne pas quand on met des
identifiants invalides. 
- La connexion ne doit pas fonctionner quand le .env n’a pas les bonnes valeurs
    - quand code (.env) incorrect (`test_azure_login_code_fake`)
    (non testable depuis github action)
    - secret client incorrect (`CLIENT_ID` .env)
    (`test_azure_login_begin_client_id_fake`)
    (non testable depuis github action)
    -`redirect_uri` incorrect (`test_azure_begin_redirect_uri_fake`)
    (non testable depuis github action)
    - `graph_user_scope` incorrect
    (`test_azure_begin_graph_user_scopes_fake`)
    (non testable depuis github action)
    - `tenant_id` incorrect (`test_azure_begin_tenant_id_fake`)
    (non testable depuis github action)
- La connexion doit continue à l’étape d’après si le .env est correct
(`test_login_begin_with_azure_account`)
- le serveur SMTP fonctionne (non testable depuis github action)
