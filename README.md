# TicketMaster - React Version

A robust ticket management web application built with Twig, featuring authentication, dashboard analytics, and full CRUD operations for ticket management.

## Live Demo

**[View Live Demo](https://ednut-ticket-twig.onrender.com/index.php)**

# Features

## Core Features

- Landing Page: Engaging hero section with wave background and decorative elements

- Authentication: Secure login/signup with form validation

- Dashboard: Overview with ticket statistics and quick actions

- Ticket Management: Complete CRUD operations (Create, Read, Update, Delete)

- Responsive Design: Optimized for mobile, tablet, and desktop

- Accessibility: WCAG compliant with proper ARIA labels and keyboard navigation

## Security & Validation

- Protected routes with session management

- Form validation with real-time error messages

- Status field validation (only accepts: "open", "in_progress", "closed")

- Input sanitization and error handling

## Design System

- Wave clip path background in hero section

- Decorative circular elements throughout

- Card-based layout with consistent shadows

- Max-width 1440px centered container

- Status-based color coding

- Fully responsive across all devices

# Installation & Setup

## Prerequisites

- PHP (8.2.12)
- composer

## Installation Steps

- git clone https://github.com/Odusami/Ednut-Ticket-Twig.git
- cd Ednut-Ticket-Twig

## Install dependencies

- php -v
    - brew install php (Mac and Linus)
    - https://www.php.net/downloads (Windows)
- composer -V
    - composer require twig/twig
    - https://getcomposer.org/download/

## Start development server

- php -S localhost:8000 -t public

# Authentication

The app uses REST backend

## Test Credentials

You can use any email and password combination, but here are some examples:

- You need to signup

# Layout

- Max Width: 1440px

- Responsive Breakpoints:

  - Mobile: 768px

  - Tablet: 1024px

  - Desktop: 1440px

# Testing

The application includes comprehensive data-testid attributes for all interactive elements to facilitate testing:

## Key Test IDs

- data-testid="auth-form" - Authentication form

- data-testid="create-ticket-btn" - Create ticket button

- data-testid="ticket-card-{id}" - Individual ticket cards

- data-testid="toast-success" - Success notifications

- data-testid="toast-error" - Error notifications
