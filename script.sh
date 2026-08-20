#!/bin/bash


touch resources/views/admin/correspondence/index.blade.php
touch resources/views/admin/correspondence/create-category.blade.php
touch resources/views/admin/correspondence/create-question.blade.php
touch resources/views/admin/correspondence/edit-question.blade.php

# Vues pour ChapterController
echo -e "${GREEN}Création des vues Chapters...${NC}"
touch resources/views/admin/chapters/create.blade.php
touch resources/views/admin/chapters/show.blade.php
touch resources/views/admin/chapters/edit.blade.php
