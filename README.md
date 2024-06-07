# Game-Finder

# Documentation de l'API Geme-Finder

## Introduction

Cette documentation décrit les points d'accès disponibles pour l'API, en se concentrant sur la gestion des utilisateurs et des événements.

## Points d'accès Utilisateur

### Obtenir une Collection d'Utilisateurs
- **URL**: `/users`
- **Méthode**: `GET`
- **Description**: Récupère une collection de tous les utilisateurs.
- **Réponse**: 
```json
[
  {
    "id": 1,
    "username": "jean_eude",
    "email": "jeaneaude@example.com"
  },
  {
    "id": 2,
    "username": "josé",
    "email": "jose@example.com"
  }
]
```
### Obtenir un Utilisateur
- **URL**: /users/{id}
- **Méthode**: GET
- **Description**: Récupère les détails d'un utilisateur spécifique.
- **Paramètres URL**:
- **id**: L'identifiant de l'utilisateur.
- **Réponse**:
{
  "id": 2,
  "username": "josé",
  "email": "jose@example.com"
}

## Points d'accès Événement

### Obtenir une Collection d'Événements
- **URL**: /events
- **Méthode**: GET
- **Description**: Récupère une collection de tous les événements.
- **Réponse**:
```json
[
  {
    "id": 1,
    "title": "Event 1",
    "date": "2023-12-25T18:00:00+00:00",
    "max_players": 10,
    "game_id": 1,
    "place": "Chez moi",
    "organizer": "/users/1"
  },
  {
    "id": 2,
    "title": "Event 2",
    "date": "2024-01-01T12:00:00+00:00",
    "max_players": 20,
    "game_id": 2,
    "place": "Chez jean-eude",
    "organizer": "/users/2"
  }
]
```


### Obtenir un Événement
- **URL**: /events/{id}
- **Méthode**: GET
- **Description**: Récupère les détails d'un événement spécifique.
- **Paramètres URL**:
- **id**: L'identifiant de l'événement.
- **Réponse**:
```json
{
  "id": 1,
  "title": "Event 1",
  "date": "2023-12-25T18:00:00+00:00",
  "max_players": 10,
  "game_id": 1,
  "place": "Chez moi",
  "organizer": "/users/1"
}
```

##Créer un Événement
- **URL**: /events
- **Méthode**: POST
- **Description**: Crée un nouvel événement.
- **Corps de la Requête**:
```json
{
  "title": "Nouvel Événement",
  "date": "2023-12-25T18:00:00+00:00",
  "max_players": 10,
  "game_id": 1,
  "place": "Chez moi",
  "organizer": "/users/1"
}
```
- **Réponse**:
```json
{
  "id": 3,
  "title": "Nouvel Événement",
  "date": "2023-12-25T18:00:00+00:00",
  "max_players": 10,
  "game_id": 1,
  "place": "Chez moi",
  "organizer": "/users/1"
}
```
