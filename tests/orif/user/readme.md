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



## azure
- La création du compte avec azure fonctionne.
    - Envoye de mail
    - Validation avec le code
    - Création du compte avec le bon nom d’utilisateur
    (`test_azure_mail_with_correct_code_new_user`)
- La connexion avec un compte azure fonctionne quand on met des identifiants
valides. 
- La connexion avec un compte azure ne fonctionne pas quand on met des
identifiants invalides.
- le serveur smtp fonctionne
