# 📡 Swap It - API Documentation

## Base URL
```
https://api.swapit.com/v1
```

## Authentication
All API requests require authentication using JWT token in the header:
```
Authorization: Bearer <token>
```

---

## 🔐 Authentication Endpoints

### 1. Register
**POST** `/auth/register`

**Request Body:**
```json
{
  "name": "John Doe",
  "email": "john@student.ac.id",
  "nim": "2141760001",
  "phone": "+62812345678",
  "password": "password123",
  "confirm_password": "password123"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Registration successful",
  "data": {
    "user": {
      "id": "user_123",
      "name": "John Doe",
      "email": "john@student.ac.id",
      "nim": "2141760001",
      "phone": "+62812345678"
    },
    "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9..."
  }
}
```

### 2. Login
**POST** `/auth/login`

**Request Body:**
```json
{
  "email": "john@student.ac.id",
  "password": "password123"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Login successful",
  "data": {
    "user": {
      "id": "user_123",
      "name": "John Doe",
      "email": "john@student.ac.id",
      "nim": "2141760001",
      "phone": "+62812345678",
      "profile_image": "https://api.swapit.com/uploads/profile_123.jpg",
      "address": "Malang, Jawa Timur"
    },
    "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9..."
  }
}
```

### 3. Logout
**POST** `/auth/logout`

**Response:**
```json
{
  "success": true,
  "message": "Logout successful"
}
```

---

## 👤 User Endpoints

### 1. Get User Profile
**GET** `/user/profile`

**Response:**
```json
{
  "success": true,
  "data": {
    "id": "user_123",
    "name": "John Doe",
    "email": "john@student.ac.id",
    "nim": "2141760001",
    "phone": "+62812345678",
    "profile_image": "https://api.swapit.com/uploads/profile_123.jpg",
    "address": "Malang, Jawa Timur",
    "created_at": "2024-01-01T00:00:00Z"
  }
}
```

### 2. Update User Profile
**PUT** `/user/profile`

**Request Body:**
```json
{
  "name": "John Doe Updated",
  "phone": "+62812345679",
  "address": "Surabaya, Jawa Timur"
}
```

---

## 📦 Item Endpoints

### 1. Get All Items
**GET** `/items`

**Query Parameters:**
- `page` (int, optional): Page number (default: 1)
- `limit` (int, optional): Items per page (default: 20)
- `category` (string, optional): Filter by category
- `search` (string, optional): Search query

**Response:**
```json
{
  "success": true,
  "data": {
    "items": [
      {
        "id": "item_123",
        "title": "Laptop Gaming ROG",
        "description": "Laptop gaming ROG Strix, RAM 16GB, SSD 512GB",
        "category": "Elektronik",
        "condition": "Bekas - Seperti Baru",
        "images": [
          "https://api.swapit.com/uploads/item_123_1.jpg",
          "https://api.swapit.com/uploads/item_123_2.jpg"
        ],
        "user_id": "user_456",
        "user_name": "Ahmad Rizki",
        "is_available": true,
        "created_at": "2024-01-15T10:30:00Z"
      }
    ],
    "pagination": {
      "current_page": 1,
      "total_pages": 5,
      "total_items": 100,
      "items_per_page": 20
    }
  }
}
```

### 2. Get Item Detail
**GET** `/items/:id`

**Response:**
```json
{
  "success": true,
  "data": {
    "id": "item_123",
    "title": "Laptop Gaming ROG",
    "description": "Laptop gaming ROG Strix, kondisi masih bagus, RAM 16GB, SSD 512GB",
    "category": "Elektronik",
    "condition": "Bekas - Seperti Baru",
    "images": [
      "https://api.swapit.com/uploads/item_123_1.jpg",
      "https://api.swapit.com/uploads/item_123_2.jpg"
    ],
    "user": {
      "id": "user_456",
      "name": "Ahmad Rizki",
      "email": "ahmad@student.ac.id",
      "phone": "+62812345678",
      "profile_image": "https://api.swapit.com/uploads/profile_456.jpg"
    },
    "is_available": true,
    "created_at": "2024-01-15T10:30:00Z",
    "updated_at": "2024-01-15T10:30:00Z"
  }
}
```

### 3. Create Item
**POST** `/items`

**Request Body (multipart/form-data):**
```
title: Laptop Gaming ROG
description: Laptop gaming ROG Strix, RAM 16GB, SSD 512GB
category: Elektronik
condition: Bekas - Seperti Baru
images[]: <file1>
images[]: <file2>
```

**Response:**
```json
{
  "success": true,
  "message": "Item created successfully",
  "data": {
    "id": "item_789",
    "title": "Laptop Gaming ROG",
    "created_at": "2024-01-20T15:00:00Z"
  }
}
```

### 4. Update Item
**PUT** `/items/:id`

### 5. Delete Item
**DELETE** `/items/:id`

### 6. Get My Items
**GET** `/items/my-items`

---

## 💼 Offer Endpoints

### 1. Get All Offers (Sent & Received)
**GET** `/offers`

**Query Parameters:**
- `type` (string): "sent" | "received" | "all"
- `status` (string, optional): "pending" | "accepted" | "rejected"

**Response:**
```json
{
  "success": true,
  "data": {
    "offers": [
      {
        "id": "offer_123",
        "item": {
          "id": "item_456",
          "title": "Laptop Gaming ROG",
          "images": ["https://api.swapit.com/uploads/item_456_1.jpg"]
        },
        "offered_item": {
          "id": "item_789",
          "title": "Macbook Pro M1",
          "images": ["https://api.swapit.com/uploads/item_789_1.jpg"]
        },
        "from_user": {
          "id": "user_123",
          "name": "John Doe"
        },
        "to_user": {
          "id": "user_456",
          "name": "Ahmad Rizki"
        },
        "message": "Saya tertarik untuk swap dengan laptop saya",
        "status": "pending",
        "created_at": "2024-01-20T10:00:00Z"
      }
    ]
  }
}
```

### 2. Create Offer
**POST** `/offers`

**Request Body:**
```json
{
  "item_id": "item_456",
  "offered_item_id": "item_789",
  "message": "Saya tertarik untuk swap dengan laptop saya"
}
```

### 3. Update Offer Status
**PUT** `/offers/:id/status`

**Request Body:**
```json
{
  "status": "accepted"
}
```

---

## 💬 Message Endpoints

### 1. Get Chat List
**GET** `/messages/chats`

**Response:**
```json
{
  "success": true,
  "data": {
    "chats": [
      {
        "id": "chat_123",
        "other_user": {
          "id": "user_456",
          "name": "Ahmad Rizki",
          "profile_image": "https://api.swapit.com/uploads/profile_456.jpg"
        },
        "last_message": "Barangnya masih available?",
        "last_message_time": "2024-01-20T15:30:00Z",
        "unread_count": 2
      }
    ]
  }
}
```

### 2. Get Messages
**GET** `/messages/:chat_id`

**Response:**
```json
{
  "success": true,
  "data": {
    "messages": [
      {
        "id": "msg_123",
        "sender_id": "user_456",
        "receiver_id": "user_123",
        "message": "Halo, barangnya masih available?",
        "is_read": true,
        "created_at": "2024-01-20T15:00:00Z"
      }
    ]
  }
}
```

### 3. Send Message
**POST** `/messages`

**Request Body:**
```json
{
  "receiver_id": "user_456",
  "message": "Ya, masih available"
}
```

### 4. Mark as Read
**PUT** `/messages/:chat_id/read`

---

## 📊 Error Responses

### Validation Error (400)
```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "email": ["Email is required"],
    "password": ["Password must be at least 6 characters"]
  }
}
```

### Unauthorized (401)
```json
{
  "success": false,
  "message": "Unauthorized. Please login"
}
```

### Not Found (404)
```json
{
  "success": false,
  "message": "Resource not found"
}
```

### Server Error (500)
```json
{
  "success": false,
  "message": "Internal server error"
}
```

---

## 📝 Notes

1. All timestamps are in ISO 8601 format (UTC)
2. File uploads support: JPG, PNG, max 5MB per file
3. Maximum 5 images per item
4. Rate limiting: 100 requests per minute per user
5. Token expiration: 30 days

---

## 🔄 WebSocket Events (Real-time)

### Connection
```
ws://api.swapit.com/ws?token=<jwt_token>
```

### Events

#### New Message
```json
{
  "event": "new_message",
  "data": {
    "id": "msg_123",
    "chat_id": "chat_456",
    "sender_id": "user_789",
    "message": "Halo!",
    "created_at": "2024-01-20T15:30:00Z"
  }
}
```

#### Offer Status Changed
```json
{
  "event": "offer_status_changed",
  "data": {
    "offer_id": "offer_123",
    "status": "accepted"
  }
}
```

---

© 2024 Swap It API Documentation
