# 📱 API Mobile App (Laravel Backend)

This repository serves as the backend engine for a Flutter-based mobile application. It provides a secure and scalable RESTful API to handle data communication between the server and the mobile client.

## 🚀 Overview
The goal of this project is to provide a seamless backend experience for mobile users, ensuring fast response times and secure data handling using Laravel's powerful ecosystem.

## 🛠️ Tech Stack
- **Framework:** [Laravel](https://laravel.com/)
- **API Style:** RESTful JSON API
- **Authentication:** [Laravel Sanctum](https://laravel.com/docs/sanctum) (or JWT)
- **Database:** MySQL
- **Testing Tools:** Postman / Insomnia

## ✨ Key Features
- **Mobile Authentication:** Secure Register/Login system specifically for mobile tokens.
- **RESTful Endpoints:** Clean and structured endpoints for fetching, creating, and updating data.
- **Middleware Security:** Protected routes to ensure only authorized mobile users can access the data.
- **Optimized JSON:** Lightweight response structures to minimize mobile data usage.

## 🔗 Connection with Flutter
This API is designed to be consumed by a **Flutter** application using the `http` or `dio` package. 

### Example Endpoint:
- `POST /api/login` - Authenticate user and return token.
- `GET /api/data` - Fetch resources for the mobile list view.

## 🚀 Installation & Setup

1. **Clone the project:**
   ```bash
   git clone [https://github.com/Tangkoan/api_mobile_app.git](https://github.com/Tangkoan/api_mobile_app.git)
   




## Facebook Clone 
    - Feature
    1. User Authentication (Login, Register, Delete Account)
    2. User Can Post Create ( Create, Update, Delete)
    3. Like Post (Like and Unlike)
    4. Comment on Post (Create, Update, Delete)

    - Additional Feature
        1. User Profile Image Upload
        2. Post Image Upload
        3. Post Pagination
        4. Post Search

## Users
    - email 
    - password
    - name 
    - profile_image
    - created_at
    - updated_at

## Posts
    - user_id
    - caption
    - image
    - created_at
    - updated_at
     

## Likes
    - user_id
    - post_id
    - created_at
    - updated_at

## Commnet
    - user_id
    - post_id
    - text
    - created_at
    - updated_at
