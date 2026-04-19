# PROJECT_WEBSITE_GALERI_FOTO
#Galeri Foto

A complete photo gallery web application built from scratch with **PHP Native**, **Tailwind CSS**, and **MySQL**. This project was developed as a school project (PKL) to demonstrate full-stack web development skills.

## Features

- **User Authentication** - Register and login with MD5 password encryption
- **Photo Upload** - Upload photos with name and description (login required)
- **Unlimited Likes** - Anyone can like photos as many times as they want (no login required)
- **Photo Gallery** - Display all photos with like counts in a responsive grid
- **Detail Popup** - Click on any photo to see details in a modal popup
- **My Photos (CRUD)** - View, edit, and delete your own photos in a table
- **User Profile** - Edit profile info, bio, and upload avatar
- **Share Profile** - Share your profile stats (total photos and likes received)
- **Search Feature** - Search photos by name or user by username
- **Responsive Design** - Fully responsive for desktop and mobile devices
- **Splash Screen** - Animated logo splash screen on page load
- **Auto-sliding Carousel** - Shows top 5 most liked photos with hover overlay effect

##  Tech Stack

- **Backend:** PHP Native (no framework)
- **Frontend:** Tailwind CSS, Font Awesome Icons
- **Database:** MySQL
- **Server:** XAMPP
- **JavaScript:** Vanilla JS (AJAX for likes)

##  Database Structure

### Tables

| Table | Description |
|-------|-------------|
| `users` | User data (id, name, email, password, avatar, bio) |
| `photos` | Photo data (id, user_id, name, description, image_path) |
| `likes` | Like data (id, photo_id, user_id) - user_id = 0 for guests |

### Relationships

- One user can have many photos (one-to-many)
- One photo can have many likes (one-to-many)

##  Installation

### Requirements

- XAMPP 
- MySQL
- Web browser

### Steps

1. Clone this repository to your htdocs folder
```bash
git clone https://github.com/yourusername/galeri-foto-native.git
