# Elite 2.0 - Collection Postman Complète

## Base URL: `http://localhost:8000/api`

---

## 🔐 1. AUTHENTIFICATION

### 1.1 Inscription
```http
POST /auth/register
Content-Type: application/json

{
    "nom": "Kamga",
    "prenom": "Jean",
    "telephone": "699000001",
    "email": "jean@email.com",
    "dernier_diplome": "BAC",
    "ville": "Douala",
    "password": "password123",
    "password_confirmation": "password123",
    "referral_code": "ELITE2024"
}

Response 201:
{
    "success": true,
    "message": "Inscription réussie",
    "data": {
        "user": {...},
        "token": "1|xxxxxxxxxxxx"
    }
}
```

### 1.2 Connexion
```http
POST /auth/login
Content-Type: application/json

{
    "telephone": "699000001",
    "password": "password123"
}

Response 200:
{
    "success": true,
    "message": "Connexion réussie",
    "data": {
        "user": {...},
        "token": "2|xxxxxxxxxxxx"
    }
}
```

### 1.3 Profil Utilisateur
```http
GET /auth/profile
Authorization: Bearer {token}

Response 200:
{
    "success": true,
    "data": {
        "id": 1,
        "nom": "Kamga",
        "prenom": "Jean",
        "telephone": "699000001",
        "email": "jean@email.com",
        "dernier_diplome": "BAC",
        "ville": "Douala",
        "solde_points": 10.00,
        "referral_code": "JEKA1234",
        "has_completed_correspondence": false,
        "has_chosen_profile": false
    }
}
```

### 1.4 Mise à jour Profil
```http
PUT /auth/profile
Authorization: Bearer {token}
Content-Type: application/json

{
    "nom": "Kamga",
    "prenom": "Jean-Pierre",
    "email": "jeanpierre@email.com",
    "ville": "Yaoundé"
}
```

### 1.5 Vérifier Code Parrainage
```http
POST /auth/check-referral-code
Content-Type: application/json

{
    "code": "ELITE2024"
}

Response 200:
{
    "success": true,
    "data": {
        "valid": true,
        "is_default": true
    }
}
```

### 1.6 Déconnexion
```http
POST /auth/logout
Authorization: Bearer {token}
```

---

## 📝 2. CORRESPONDANCE (Auth Required)

### 2.1 Liste des Questions
```http
GET /correspondence/questions
Authorization: Bearer {token}

Response 200:
{
    "success": true,
    "data": [
        {
            "category": "Personnalité et Comportement",
            "questions": [
                {
                    "id": 1,
                    "texte": "Comment gérez-vous le stress au quotidien ?",
                    "type": "qcm",
                    "ordre": 1,
                    "obligatoire": true,
                    "answers": [
                        {"id": 1, "texte": "Je reste calme et j'analyse la situation", "ordre": 0},
                        {"id": 2, "texte": "Je cherche du soutien auprès des autres", "ordre": 1}
                    ]
                }
            ]
        }
    ]
}
```

### 2.2 Soumettre Réponses
```http
POST /correspondence/submit
Authorization: Bearer {token}
Content-Type: application/json

{
    "responses": [
        {"question_id": 1, "answer_id": 2},
        {"question_id": 2, "answer_id": 5},
        {"question_id": 3, "answer_id": 9},
        {"question_id": 4, "answer_id": 14}
    ]
}

Response 200:
{
    "success": true,
    "message": "Réponses enregistrées",
    "data": {
        "profiles_count": 5
    }
}
```

### 2.3 Résultats de Correspondance
```http
GET /correspondence/results
Authorization: Bearer {token}

Response 200:
{
    "success": true,
    "data": {
        "recommended_profiles": [
            {
                "profile": {
                    "id": 3,
                    "nom": "Marketing Digital",
                    "secteur": "Digital",
                    "description": "...",
                    "niveau_minimum": "BAC",
                    "debouches": ["Community Manager", "Growth Hacker"]
                },
                "score": 85.5,
                "rank": 1
            }
        ],
        "all_profiles": [...]
    }
}
```

### 2.4 Choisir un Profil
```http
POST /correspondence/choose-profile
Authorization: Bearer {token}
Content-Type: application/json

{
    "profile_id": 3
}

Response 200:
{
    "success": true,
    "message": "Profil sélectionné avec succès"
}
```

### 2.5 Choisir le Mode de Parcours
```http
POST /correspondence/choose-path
Authorization: Bearer {token}
Content-Type: application/json

{
    "mode": "en_ligne"
}

// Modes disponibles: "en_ligne", "presentiel", "externe"

Response 200:
{
    "success": true,
    "message": "Mode de parcours enregistré"
}
```

---

## 🎯 3. PROFILS & ROADMAPS

### 3.1 Liste des Profils Métiers
```http
GET /profiles
GET /profiles?secteur=Digital
GET /profiles?is_cfpam=true

Response 200:
{
    "success": true,
    "data": [
        {
            "id": 1,
            "nom": "Secrétariat Bureautique",
            "slug": "secretariat-bureautique",
            "secteur": "Secrétariat",
            "niveau_minimum": "BEPC",
            "is_cfpam": true,
            "debouches": ["Assistant administratif", "Agent de saisie"]
        }
    ]
}
```

### 3.2 Détail d'un Profil
```http
GET /profiles/{id}
/roadmap
Response 200:
{
    "success": true,
    "data": {
        "id": 3,
        "nom": "Marketing Digital",
        "description": "Formation complète en marketing digital...",
        "secteur": "Digital",
        "niveau_minimum": "BAC",
        "debouches": [...],
        "packs": [...]
    }
}
```

### 3.3 Roadmap pour un Profil
```http
GET /profiles/{id}?niveau=BAC
Authorization: Bearer {token}

Response 200:
{
    "success": true,
    "data": {
        "profile": {...},
        "roadmap": {
            "titre": "Parcours Marketing Digital - Niveau BAC",
            "duree_estimee_mois": 12,
            "steps": [
                {
                    "ordre": 1,
                    "titre": "Fondamentaux du Marketing",
                    "description": "...",
                    "duree_semaines": 4,
                    "pack_recommande": {...}
                }
            ]
        }
    }
}
```

---

## 📚 4. COURS & PACKS

### 4.1 Liste des Catégories
```http
GET /categories

Response 200:
{
    "success": true,
    "data": [
        {
            "id": 1,
            "nom": "Secrétariat",
            "slug": "secretariat",
            "packs_count": 3
        }
    ]
}
```

### 4.2 Liste des Packs
```http
GET /packs
GET /packs?category_id=1
GET /packs?niveau=BAC

Response 200:
{
    "success": true,
    "data": [
        {
            "id": 1,
            "nom": "Bureautique",
            "category": "Secrétariat",
            "niveau_requis": "BEPC",
            "prix_points": 50,
            "durees_disponibles": ["3 mois", "6 mois"],
            "diplomes_possibles": ["AQP", "CQP"],
            "total_lessons": 45,
            "total_duration_minutes": 1200
        }
    ]
}
```

### 4.3 Packs Recommandés (basé sur profil choisi)
```http
GET /packs/recommended
Authorization: Bearer {token}

Response 200:
{
    "success": true,
    "data": [
        {
            "pack": {...},
            "priority": 1
        }
    ]
}
```

### 4.4 Détail d'un Pack
```http
GET /packs/{id}

Response 200:
{
    "success": true,
    "data": {
        "id": 1,
        "nom": "Bureautique",
        "description": "...",
        "niveau_requis": "BEPC",
        "prix_points": 50,
        "durees_disponibles": ["3 mois", "6 mois"],
        "diplomes_possibles": ["AQP", "CQP"],
        "debouches": [...],
        "modules_count": 5,
        "total_lessons": 45
    }
}
```

### 4.5 Acheter un Pack
```http
POST /packs/{id}/purchase
Authorization: Bearer {token}
Content-Type: application/json

{
    "duree": "6 mois"
}

Response 200:
{
    "success": true,
    "message": "Pack acheté avec succès",
    "data": {
        "user_pack_id": 1,
        "date_expiration": "2025-06-30"
    }
}

Response 400:
{
    "success": false,
    "message": "Solde insuffisant. Vous avez 10 points, le pack coûte 50 points."
}
```

### 4.6 Mes Packs Achetés
```http
GET /user/packs
Authorization: Bearer {token}

Response 200:
{
    "success": true,
    "data": [
        {
            "id": 1,
            "pack": {...},
            "duree_choisie": "6 mois",
            "statut": "actif",
            "progression": 25.5,
            "date_achat": "2024-12-01",
            "date_expiration": "2025-06-01"
        }
    ]
}
```

### 4.7 Modules d'un Pack
```http
GET /packs/{id}/modules
Authorization: Bearer {token}

Response 200:
{
    "success": true,
    "data": [
        {
            "id": 1,
            "nom": "Introduction à la bureautique",
            "description": "...",
            "type": "theorique",
            "ordre": 1,
            "chapters_count": 4,
            "is_unlocked": true
        }
    ]
}
```

### 4.8 Chapitres d'un Module
```http
GET /modules/{id}/chapters
Authorization: Bearer {token}

Response 200:
{
    "success": true,
    "data": [
        {
            "id": 1,
            "nom": "Les bases de Word",
            "description": "...",
            "ordre": 1,
            "note_passage": 14,
            "lessons_count": 5,
            "has_quiz": true,
            "is_unlocked": true,
            "is_completed": false
        }
    ]
}
```

### 4.9 Leçons d'un Chapitre
```http
GET /chapters/{id}/lessons
Authorization: Bearer {token}

Response 200:
{
    "success": true,
    "data": [
        {
            "id": 1,
            "titre": "Interface de Word",
            "contenu_texte": "...",
            "url_video": "https://youtube.com/...",
            "url_externe": null,
            "duree_minutes": 15,
            "ordre": 1,
            "is_completed": false
        }
    ]
}
```

### 4.10 Détail d'une Leçon
```http
GET /lessons/{id}
Authorization: Bearer {token}

Response 200:
{
    "success": true,
    "data": {
        "id": 1,
        "titre": "Interface de Word",
        "contenu_texte": "<p>Contenu HTML de la leçon...</p>",
        "url_video": "https://youtube.com/watch?v=xxx",
        "url_externe": null,
        "duree_minutes": 15,
        "is_completed": false,
        "chapter": {...},
        "next_lesson": {...},
        "previous_lesson": {...}
    }
}
```

### 4.11 Marquer Leçon Complétée
```http
POST /lessons/{id}/complete
Authorization: Bearer {token}

Response 200:
{
    "success": true,
    "message": "Leçon marquée comme complétée",
    "data": {
        "chapter_progress": 40.0,
        "pack_progress": 15.5
    }
}
```

### 4.12 Quiz d'un Chapitre
```http
GET /chapters/{id}/quiz
Authorization: Bearer {token}

Response 200:
{
    "success": true,
    "data": {
        "id": 1,
        "titre": "Quiz - Les bases de Word",
        "description": "Testez vos connaissances",
        "duree_minutes": 30,
        "questions": [
            {
                "id": 1,
                "enonce": "Quel raccourci permet de sauvegarder ?",
                "type": "qcm",
                "points": 2,
                "answers": [
                    {"id": 1, "texte": "Ctrl + S"},
                    {"id": 2, "texte": "Ctrl + P"},
                    {"id": 3, "texte": "Ctrl + C"}
                ]
            }
        ]
    }
}
```

### 4.13 Soumettre Quiz
```http
POST /quiz/{id}/submit
Authorization: Bearer {token}
Content-Type: application/json

{
    "responses": [
        {"question_id": 1, "answer_id": 1},
        {"question_id": 2, "answer_id": 5},
        {"question_id": 3, "answer_id": 9}
    ]
}

Response 200:
{
    "success": true,
    "data": {
        "note": 16.5,
        "note_sur": 20,
        "reussi": true,
        "bonnes_reponses": 8,
        "total_questions": 10,
        "corrections": [
            {
                "question_id": 1,
                "votre_reponse": 1,
                "bonne_reponse": 1,
                "correct": true,
                "explication": "Ctrl + S est le raccourci universel de sauvegarde"
            }
        ],
        "chapitre_suivant_debloque": true
    }
}

Response 200 (échec):
{
    "success": true,
    "data": {
        "note": 8.0,
        "reussi": false,
        "options": {
            "recommencer": true,
            "parrainage_requis": 4,
            "filleuls_actuels": 1
        }
    }
}
```

---

## 💰 5. WALLET & PAIEMENTS

### 5.1 Solde du Wallet
```http
GET /wallet/balance
Authorization: Bearer {token}

Response 200:
{
    "success": true,
    "data": {
        "solde_points": 45.50,
        "equivalent_fcfa": 22750
    }
}
```

### 5.2 Effectuer un Dépôt
```http
POST /wallet/deposit
Authorization: Bearer {token}
Content-Type: application/json

{
    "montant_fcfa": 5000
}

Response 200:
{
    "success": true,
    "message": "Dépôt effectué avec succès",
    "data": {
        "montant_fcfa": 5000,
        "points_credites": 10,
        "nouveau_solde": 55.50,
        "reference": "TXN-ABCD1234EFGH"
    }
}
```

### 5.3 Utiliser un Code Caisse
```http
POST /wallet/use-cash-code
Authorization: Bearer {token}
Content-Type: application/json

{
    "code": "CASH-XXXXXXXX"
}

Response 200:
{
    "success": true,
    "message": "Code utilisé avec succès",
    "data": {
        "points_credites": 20,
        "nouveau_solde": 75.50
    }
}

Response 400:
{
    "success": false,
    "message": "Code invalide ou déjà utilisé"
}
```

### 5.4 Trouver un Utilisateur (pour transfert)
```http
POST /wallet/find-user
Authorization: Bearer {token}
Content-Type: application/json

{
    "telephone": "699000002"
}

Response 200:
{
    "success": true,
    "data": {
        "id": 2,
        "nom": "Nguemo",
        "prenom": "Marie",
        "ville": "Yaoundé"
    }
}

Response 404:
{
    "success": false,
    "message": "Utilisateur non trouvé"
}
```

### 5.5 Effectuer un Transfert
```http
POST /wallet/transfer
Authorization: Bearer {token}
Content-Type: application/json

{
    "telephone": "699000002",
    "points": 10,
    "motif": "Aide pour formation"
}

Response 200:
{
    "success": true,
    "message": "Transfert effectué avec succès",
    "data": {
        "destinataire": "Marie Nguemo",
        "points_envoyes": 10,
        "nouveau_solde": 65.50,
        "reference": "TRF-WXYZ5678IJKL"
    }
}

Response 400:
{
    "success": false,
    "message": "Solde insuffisant"
}
```

### 5.6 Historique des Transactions
```http
GET /wallet/transactions
GET /wallet/transactions?type=depot
GET /wallet/transactions?page=2
Authorization: Bearer {token}

Response 200:
{
    "success": true,
    "data": {
        "transactions": [
            {
                "id": 1,
                "type": "depot",
                "montant_fcfa": 5000,
                "points": 10,
                "reference": "TXN-ABCD1234EFGH",
                "description": "Dépôt par mobile money",
                "statut": "complete",
                "created_at": "2024-12-15T10:30:00Z"
            },
            {
                "id": 2,
                "type": "achat_pack",
                "points": -50,
                "reference": "TXN-MNOP9012QRST",
                "description": "Achat pack Bureautique",
                "statut": "complete",
                "created_at": "2024-12-15T11:00:00Z"
            }
        ],
        "pagination": {
            "current_page": 1,
            "total_pages": 3,
            "total": 25
        }
    }
}
```

---

## 👥 6. PARRAINAGE

### 6.1 Mon Code de Parrainage
```http
GET /referral/my-code
Authorization: Bearer {token}

Response 200:
{
    "success": true,
    "data": {
        "code": "JEKA1234",
        "share_message": "Rejoins Elite 2.0 avec mon code JEKA1234 et commence ta formation professionnelle !"
    }
}
```

### 6.2 Statistiques de Parrainage
```http
GET /referral/stats
Authorization: Bearer {token}

Response 200:
{
    "success": true,
    "data": {
        "total_filleuls": 12,
        "points_gagnes": 12,
        "filleuls_ce_mois": 3
    }
}
```

### 6.3 Historique des Parrainages
```http
GET /referral/history
Authorization: Bearer {token}

Response 200:
{
    "success": true,
    "data": [
        {
            "filleul": {
                "prenom": "Paul",
                "nom": "Nkoulou",
                "ville": "Douala"
            },
            "points_gagnes": 1,
            "date_inscription": "2024-12-10T08:00:00Z"
        }
    ]
}
```

---

## 🧪 Scénario de Test Complet

1. **Inscription** → POST /auth/register
2. **Connexion** → POST /auth/login (récupérer token)
3. **Questions correspondance** → GET /correspondence/questions
4. **Soumettre réponses** → POST /correspondence/submit
5. **Voir résultats** → GET /correspondence/results
6. **Choisir profil** → POST /correspondence/choose-profile
7. **Choisir mode** → POST /correspondence/choose-path
8. **Voir roadmap** → GET /profiles/{id}/roadmap
9. **Faire un dépôt** → POST /wallet/deposit
10. **Acheter un pack** → POST /packs/{id}/purchase
11. **Voir contenu** → GET /packs/{id}/modules → GET /chapters/{id}/lessons
12. **Compléter leçons** → POST /lessons/{id}/complete
13. **Passer quiz** → POST /quiz/{id}/submit
