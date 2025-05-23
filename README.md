# AtlanStream

## Configuration de la base de données

Pour permettre aux films d'avoir plusieurs catégories, il est nécessaire de créer une table de jointure `movie_categories`. Voici la requête SQL:

```sql
CREATE TABLE movie_categories (
    movie_id INT NOT NULL,
    category_id INT NOT NULL,
    PRIMARY KEY (movie_id, category_id),
    FOREIGN KEY (movie_id) REFERENCES movies(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
);
```

Pour mettre à jour la table `movies` avec les nouveaux champs:

```sql
ALTER TABLE movies 
ADD COLUMN year INT DEFAULT NULL,
ADD COLUMN duration INT DEFAULT 0,
ADD COLUMN director VARCHAR(100) DEFAULT NULL,
ADD COLUMN actors TEXT DEFAULT NULL;
```

## Migrations depuis l'ancien système

Si vous utilisez l'ancien système où chaque film avait une seule catégorie (via category_id), vous pouvez migrer les données avec cette requête:

```sql
INSERT INTO movie_categories (movie_id, category_id)
SELECT id, category_id FROM movies 
WHERE category_id IS NOT NULL;
```