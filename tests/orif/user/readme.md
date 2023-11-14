# Liste de test
- La connexion avec un compte local fonctionne quand on met des identifiants
valides.
- La connexion avec un compte local ne fonctionne pas quand on met des
identifiants invalides.

- L’ajout d’un nouvel utilisateur fonctionne quand on est connecté avec un
compte administrateur. (testsave_userWithUserId)
- L’ajout d’un nouvel utilisateur ne fonctionne pas quand on n’est pas connecté
avec un compte administrateur.
- L’édition d’un utilisateur fonctionne quand on est connecté avec un compte
administrateur. (testsave_userWithUserIdWithSameSessionUserId)
- L’édition d’un utilisateur ne fonctionne pas quand on n’est pas connecté avec
un compte administrateur.
- La suppression d’un utilisateur fonctionne quand on est connecté avec un
compte administrateur. (testdelete_userWitDeleteAction)
- La suppression d’un utilisateur ne fonctionne pas quand on n’est pas connecté
avec un compte administrateur. (testdelete_userWithoutSession)
- La désactivation d’un utilisateur fonctionne quand on est connecté avec un
compte administrateur. (testdelete_userWitDisableAction)
- La désactivation d’un utilisateur ne fonctionne pas quand on n’est pas
connecté avec un compte administrateur.
- La réactivation d’un utilisateur fonctionne quand on est connecté avec un
compte administrateur. (testreactivate_userWithExistingUser)
- La réactivation d’un utilisateur ne fonctionne pas quand on n’est pas
connecté avec un compte administrateur.
 
- Mettre un utilisateur en administrateur fonctionne quand on est connecté avec
un compte administrateur. (testsave_userWithUserId)
- Mettre un utilisateur en administrateur ne fonctionne pas quand on n’est pas
connecté avec un compte administrateur.
- L'affichage de la liste des utilisateurs fonctionne quand on est connecté
avec un compte administrateur. (testlist_userWithAdministratorSession)
- L'affichage de la liste des utilisateurs ne fonctionne pas quand on n’est pas
connecté avec un compte administrateur.
 
- Modifier le mot de passe fonctionne quand on est connecté à un compte.
(testpassword_change_user)
- Modifier le mot de passe ne fonctionne pas quand on n’est pas connecté à un
compte. 

- Se déconnecter

