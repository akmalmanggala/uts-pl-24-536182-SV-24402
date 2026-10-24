# syntax=docker/dockerfile:1
# ---------------------------------------------------------
# Stage 1: Build Vue SPA Application
# Base image locked to specific tag node:20-alpine
# ---------------------------------------------------------
FROM node:20-alpine AS build-stage

WORKDIR /app

# Layer Caching: Copy package definitions first
COPY package.json ./

# Install dependencies cleanly
RUN npm install

# Copy application source code
COPY . .

# Build argument for API URL with default fallback
ARG VITE_API_URL=http://localhost:8000/api
ENV VITE_API_URL=${VITE_API_URL}

# Build static assets to dist/
RUN npm run build

# ---------------------------------------------------------
# Stage 2: Serve static files using Nginx
# Base image locked to specific tag nginx:1.27-alpine
# ---------------------------------------------------------
FROM nginx:1.27-alpine AS production-stage

# Remove default nginx html
RUN rm -rf /usr/share/nginx/html/*

# Copy built static artifacts from build-stage (no node_modules!)
COPY --from=build-stage /app/dist /usr/share/nginx/html

# Copy custom Nginx configuration
COPY nginx.conf /etc/nginx/conf.d/default.conf

EXPOSE 80

CMD ["nginx", "-g", "daemon off;"]
