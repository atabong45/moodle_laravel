

# Consignes Indispensables pour Faire Fonctionner le Projet

## Configuration des Services Externes du Serveur Moodle

(### Une Autre Approche
Une alternative consiste à :

1. Aller dans **Site Administration** → **Serveur** → **Services Web** → **Aperçu**.
2. Sélectionner **Utilisateurs comme clients avec un token**.
3. Suivre toutes les étapes détaillées dans cette section.
4. Tester avec un ancien projet Moodle pour vérifier si tout fonctionne correctement.)


# Utilisateurs en tant que clients avec token

Les étapes suivantes vous aident à configurer le service web Moodle pour les utilisateurs en tant que clients. Ces étapes incluent la configuration de l'authentification recommandée via token (clés de sécurité). L'utilisateur générera son token à partir de la page des préférences.

## Étapes à suivre

### 1. Activer les services web
**Description :** Les services web doivent être activés dans les fonctionnalités avancées.  
**Étapes :**
1. Connectez-vous en tant qu'administrateur sur votre serveur Moodle.
2. Accédez à **Administration du site > Fonctionnalités avancées**.
3. Activez l'option **Activer les services web**.
4. Cliquez sur **Enregistrer les modifications**.

---

### 2. Activer les protocoles
**Description :** Au moins un protocole doit être activé. Pour des raisons de sécurité, seuls les protocoles nécessaires doivent être activés.  
**Étapes :**
1. Rendez-vous sur **Administration du site > Plugins > Services web > Gérer les protocoles**.
2. Activez le protocole **REST** en cliquant sur l'icône d'activation.
3. Désactivez les protocoles inutilisés pour sécuriser le système.
4. Enregistrez vos modifications.

---

### 3. Sélectionner un service
**Description :** Un service est un ensemble de fonctions de services web. Vous allez permettre aux utilisateurs d'accéder à un nouveau service.  
**Étapes :**
1. Allez dans **Administration du site > Plugins > Services web > Gérer les services**.
2. Cliquez sur **Ajouter un service**.
3. Nommez le service (par exemple appeler le moodle ) et cochez **Activer**.
4. Décochez **Utilisateurs autorisés uniquement**.
5. Cliquez sur **Enregistrer les modifications**.

---

### 4. Ajouter des fonctions
**Description :** Sélectionnez les fonctions nécessaires pour le nouveau service créé.  
**Étapes :**
1. Après avoir créé le service, cliquez sur **Ajouter des fonctions** dans la page de gestion du service.
2. Choisissez les fonctions appropriées dans la liste déroulante (ex. : création d'utilisateurs, récupération de cours, etc.).
3. Cliquez sur **Ajouter une fonction**.
4. Répétez pour toutes les fonctions nécessaires.

---

### 5. Vérifier les capacités des utilisateurs
**Description :** Les utilisateurs doivent avoir deux capacités : `webservice:createtoken` et une capacité correspondant au protocole utilisé (par exemple `webservice/rest:use` pour REST).  
**Étapes :**
1. Créez un rôle spécifique pour le service web via **Administration du site > Utilisateurs > Permissions > Définir des rôles**.
2. Cliquez sur **Ajouter un rôle** et configurez-le avec les capacités suivantes :
   - **webservice:createtoken**
   - **webservice/rest:use**
3. Assignez ce rôle à l'utilisateur devant accéder au service web via **Administration du site > Utilisateurs > Permissions > Attribuer des rôles système**.

---

### 6. Tester le service
**Description :** Simulez l'accès externe au service en utilisant un client de test des services web.  
**Étapes :**
1. Connectez-vous en tant qu'utilisateur ayant la capacité **webservice:createtoken**.
2. Accédez à vos préférences utilisateur et générez un **token** via la section **Clés de sécurité**.
3. Utilisez un outil comme Postman ou Curl pour tester le service en utilisant le token généré.
4. Assurez-vous de sélectionner un protocole activé lors du test.
(autre methode de tester)
 entrer cette url sur votre navigateur en remplacant [votre token] par la valeur de votre token sans les crochets bien sur
recuperer les tous les utilisateurs
http://localhost/webservice/rest/server.php?wstoken=[votre token]&wsfunction=core_user_get_users&moodlewsrestformat=json&criteria[0][key]=email&criteria[0][value]=%
rasurer vous d'avoir ajouter la fonction core_user_get_users  dans votre service moodle


### 7. Générer et copier un token avec l'utilisateur administrateur
**Description :** L'administrateur peut générer un token pour lui-même ou pour d'autres utilisateurs et le copier pour l'utiliser dans les tests ou les intégrations.  
**Étapes :**
1. Connectez-vous sur Moodle en tant qu'administrateur.
2. Accédez à **Administration du site > Plugins > Services web > Gérer les tokens**.
3. Cliquez sur **Créer un token**.
4. Renseignez les informations suivantes :
   - **Utilisateur :** Sélectionnez "Administrateur" ou l'utilisateur concerné.
   - **Service :** Choisissez le service que vous avez configuré précédemment.
   - **Date d’expiration (optionnel) :** Définissez une date si nécessaire.
5. Cliquez sur **Enregistrer les modifications**.
6. Une fois le token généré, copiez-le et conservez-le dans un endroit sûr.

⚠️ **Remarque :** Ce token est essentiel pour accéder aux services web. Ne le partagez qu'avec les utilisateurs ou systèmes autorisés.


une fois le token copier, le mettre dans les variables d'environnement, rajouter egalement l'url de l'api afin d'avoir quelquechose de ce format dans votre fichier .env sur votre projet laravel 

MOODLE_API_URL=http://localhost/webservice/rest/server.php
MOODLE_API_TOKEN=8f64ca18b0aa92ced02421165c003d24
