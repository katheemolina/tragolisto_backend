<img src="https://github.com/user-attachments/assets/f928b50b-a256-4afa-a83b-1296cd9f4a7f" style="width: 100%; display: block;" />

# Backend - Proyecto TragoListo

## Instalación y puesta en marcha del backend

Sigue estos pasos para clonar, configurar y levantar el proyecto localmente:

1. **Clonar el repositorio**

```bash
git clone https://github.com/katheemolina/tragolisto_backend
```

2. **Levantar el servidor PHP embebido**
   
Dentro de la carpeta del proyecto ejecuta:
```bash
php -S localhost:8000 -t public
```
3. **Documentación de endpoints**

La documentación completa de los endpoints está disponible en Postman. Puedes unirte a la colección y equipo con el siguiente enlace: a colección y equipo con el siguiente enlace: [Unirse a la colección Postman](https://app.getpostman.com/join-team?invite_code=556adbd917b22317a9397f5e3d7a2a932a859c49ef3ab51be425ba8612ef26b4&target_code=f9d286767f27a8d953ccb5f650e644fa)

4. **Configuración de la base de datos**

- Abre **phpMyAdmin** en tu servidor local (por ejemplo, XAMPP).
- Crea una base de datos nueva llamada **tragolisto_db**.
- Ejecuta el siguiente script SQL para crear las tablas necesarias e insertar datos de ejemplo:

```bash
CREATE TABLE usuarios (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    google_id VARCHAR(255) UNIQUE NOT NULL,
    nombre VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    fecha_nacimiento DATE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

INSERT INTO usuarios (google_id, nombre, email, fecha_nacimiento) VALUES
('google_id_nico', 'Nico Paz', 'nico.paz@example.com', '1995-03-15'),
('google_id_tomas', 'Tomás Suarez', 'tomas.suarez@example.com', '1998-07-22'),
('google_id_valen', 'Valentina Ruiz', 'valen.ruiz@example.com', '1997-11-01');

CREATE TABLE ingredientes (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255) UNIQUE NOT NULL,
    es_alcohol BOOLEAN DEFAULT FALSE,
    categoria VARCHAR(100),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

INSERT INTO ingredientes (nombre, es_alcohol, categoria) VALUES
('Ron Blanco', TRUE, 'Alcohol Base'),
('Vodka', TRUE, 'Alcohol Base'),
('Tequila Blanco', TRUE, 'Alcohol Base'),
('Gin', TRUE, 'Alcohol Base'),
('Whisky', TRUE, 'Alcohol Base'),
('Cerveza Lager', TRUE, 'Alcohol Base'),
('Vino Tinto', TRUE, 'Alcohol Base'),
('Vino Blanco', TRUE, 'Alcohol Base'),
('Aperol', TRUE, 'Aperitivo'),
('Fernet', TRUE, 'Aperitivo'),
('Vermouth Rojo', TRUE, 'Aperitivo'),
('Cointreau', TRUE, 'Licor'),
('Licor de Café', TRUE, 'Licor'),
('Campari', TRUE, 'Aperitivo'),
('Jugo de Naranja', FALSE, 'Jugo de Fruta'),
('Jugo de Limón', FALSE, 'Jugo de Fruta'),
('Jugo de Lima', FALSE, 'Jugo de Fruta'),
('Jugo de Pomelo', FALSE, 'Jugo de Fruta'),
('Jugo de Piña', FALSE, 'Jugo de Fruta'),
('Naranja', FALSE, 'Fruta Cítrica'),
('Limón', FALSE, 'Fruta Cítrica'),
('Lima', FALSE, 'Fruta Cítrica'),
('Menta', FALSE, 'Hierba Aromática'),
('Pepino', FALSE, 'Vegetal'),
('Frutos Rojos', FALSE, 'Fruta'),
('Agua con gas', FALSE, 'Gaseosa/Soda'),
('Coca Cola', FALSE, 'Gaseosa/Soda'),
('Sprite', FALSE, 'Gaseosa/Soda'),
('Tónica', FALSE, 'Gaseosa/Soda'),
('Agua natural', FALSE, 'Agua'),
('Azúcar', FALSE, 'Endulzante'),
('Miel', FALSE, 'Endulzante'),
('Granadina', FALSE, 'Jarabes'),
('Hielo', FALSE, 'Adicional'),
('Sal', FALSE, 'Especias/Condimentos'),
('Jengibre', FALSE, 'Especias/Condimentos');

CREATE TABLE tragos (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255) UNIQUE NOT NULL,
    descripcion TEXT,
    instrucciones TEXT NOT NULL,
    tips TEXT,
    historia TEXT,
    es_alcoholico BOOLEAN NOT NULL,
    imagen_url VARCHAR(255),
    dificultad VARCHAR(50),
    tiempo_preparacion_minutos INT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);


INSERT INTO tragos (nombre, descripcion, instrucciones, tips, historia, es_alcoholico, dificultad, tiempo_preparacion_minutos) VALUES
('Fernet con Coca', 'El clásico argentino, simple, refrescante y con un toque amargo.',
'1. Llena un vaso de trago largo con hielo.
2. Agrega 30% de Fernet.
3. Completa con Coca Cola, dejando un espacio para la espuma.
4. Revuelve suavemente',
'La proporción clásica es 70% Coca Cola, 30% Fernet, pero puedes ajustarla a tu gusto. El vaso debe estar bien frío.',
'Originario de Italia, el Fernet Branca se popularizó enormemente en Argentina, especialmente combinado con Coca Cola, convirtiéndose en un ícono cultural.', TRUE, 'Muy Fácil', 2),

('Gin Tonic', 'Un clásico británico, amargo y floral, perfecto para cualquier momento.',
'1. Llena una copa balón o vaso alto con mucho hielo.
2. Vierte 2 oz de Gin.
3. Completa con 4 oz de Tónica fría.
4. Exprime suavemente un gajo de lima y déjalo caer en la copa.
5. Remueve brevemente.',
'Usa un buen gin y una tónica de calidad para realzar los sabores. Puedes decorar con rodajas de pepino o bayas de enebro.',
'Surgió en la India colonial británica, donde la quinina de la tónica se usaba para combatir la malaria, y el gin se le añadió para mejorar el sabor.', TRUE, 'Fácil', 3),

('Limonada con Menta y Jengibre', 'Una limonada casera con un toque picante y refrescante.',
'1. En una jarra, machaca suavemente unas rodajas de jengibre fresco y hojas de menta.
2. Agrega el jugo de 4 limones y 3 cucharadas de azúcar (o a gusto).
3. Revuelve hasta disolver el azúcar.
4. Completa la jarra con agua fría y mucho hielo.
5. Mezcla bien y decora con rodajas de limón y ramitas de menta.',
'Ajusta la cantidad de jengibre según la intensidad que desees. Puedes usar agua con gas para un toque efervescente.',
'La limonada ha sido una bebida popular en muchas culturas, y la adición de menta y jengibre la ha convertido en una opción moderna y revitalizante.', FALSE, 'Fácil', 5),

('Destornillador (Screwdriver)', 'Sencillo y directo, el favorito de quienes buscan algo rápido y con sabor a naranja.',
'1. Llena un vaso alto con hielo.
2. Vierte 2 oz de Vodka.
3. Completa con Jugo de Naranja natural.
4. Remueve suavemente.
5. Decora con una rodaja de naranja.',
'Usa jugo de naranja recién exprimido para un sabor óptimo. Sirve bien frío.',
'Se cree que el nombre surgió en los años 40 en campos petroleros, donde los trabajadores usaban un destornillador para mezclar su vodka con jugo de naranja en lata.', TRUE, 'Muy Fácil', 2),

('Refresco de Frutos Rojos', 'Bebida sin alcohol, dulce y ligeramente ácida, muy colorida y refrescante.',
'1. En un vaso, machaca suavemente unos frutos rojos (frescos o descongelados) con una cucharadita de azúcar.
2. Agrega mucho hielo.
3. Vierte 1 oz de jugo de limón.
4. Completa con agua con gas o Sprite.
5. Remueve suavemente y decora con más frutos rojos y una hojita de menta.',
'Puedes ajustar el dulzor según tu preferencia. Ideal para una tarde calurosa o como alternativa sin alcohol.',
'Las bebidas a base de frutas y soda son populares en muchas culturas, y los frutos rojos aportan un sabor vibrante y antioxidantes.', FALSE, 'Fácil', 4);

CREATE TABLE tragos_ingredientes (
    trago_id BIGINT NOT NULL,
    ingrediente_id BIGINT NOT NULL,
    cantidad VARCHAR(100),
    unidad VARCHAR(50),
    notas VARCHAR(255),
    PRIMARY KEY (trago_id, ingrediente_id),
    FOREIGN KEY (trago_id) REFERENCES tragos(id) ON DELETE CASCADE,
    FOREIGN KEY (ingrediente_id) REFERENCES ingredientes(id) ON DELETE CASCADE
);

-- INSERTS PARA TRAGOS_INGREDIENTES

-- Ingredientes para Fernet con Coca (Ej: ID 1 de Trago, IDs de Ingredientes que obtuviste)
INSERT INTO tragos_ingredientes (trago_id, ingrediente_id, cantidad, unidad) VALUES
(1, 10, '30', '%'), -- Fernet
(1, 27, '70', '%'), -- Coca Cola
(1, 34, 'c/n', 'cubos'); -- Hielo

-- Ingredientes para Gin Tonic (Ej: ID 2 de Trago)
INSERT INTO tragos_ingredientes (trago_id, ingrediente_id, cantidad, unidad) VALUES
(2, 4, '2', 'oz'),        -- Gin
(2, 29, '4', 'oz'),      -- Tónica
(2, 22, '1', 'gajo'),     -- Lima
(2, 34, 'c/n', 'cubos'); -- Hielo

-- Ingredientes para Limonada con Menta y Jengibre (Ej: ID 3 de Trago)
INSERT INTO tragos_ingredientes (trago_id, ingrediente_id, cantidad, unidad) VALUES
(3, 21, '4', 'unidades'),  -- Limón (jugo de 4 limones)
(3, 31, '3', 'cucharadas'), -- Azúcar
(3, 23, 'c/n', 'hojas'),  -- Menta
(3, 36, 'c/n', 'rodajas'), -- Jengibre
(3, 30, 'c/n', 'ml'), -- Agua natural
(3, 34, 'c/n', 'cubos');   -- Hielo

-- Ingredientes para Destornillador (Screwdriver) (Ej: ID 4 de Trago)
INSERT INTO tragos_ingredientes (trago_id, ingrediente_id, cantidad, unidad) VALUES
(4, 2, '2', 'oz'),        -- Vodka
(4, 15, 'c/n', 'ml'), -- Jugo de Naranja
(4, 34, 'c/n', 'cubos'); -- Hielo

-- Ingredientes para Refresco de Frutos Rojos (Ej: ID 5 de Trago)
INSERT INTO tragos_ingredientes (trago_id, ingrediente_id, cantidad, unidad) VALUES
(5, 25, 'c/n', 'gramos'), -- Frutos Rojos
(5, 31, '1', 'cucharadita'), -- Azúcar
(5, 16, '1', 'oz'),    -- Jugo de Limón
(5, 26, 'c/n', 'ml'), -- Agua con gas
(5, 34, 'c/n', 'cubos');    -- Hielo

CREATE TABLE favoritos (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT NOT NULL,
    trago_id BIGINT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at DATETIME DEFAULT NULL, 
    UNIQUE (user_id, trago_id),
    FOREIGN KEY (user_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (trago_id) REFERENCES tragos(id) ON DELETE CASCADE
);

INSERT INTO favoritos (user_id, trago_id) VALUES
(1, 1), 
(1, 2), 
(2, 1), 
(2, 3); 

CREATE TABLE juegos (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255) UNIQUE NOT NULL,
    descripcion TEXT NOT NULL,
    categoria VARCHAR(100),
    materiales TEXT,
    min_jugadores INT,
    max_jugadores INT,
    es_para_beber BOOLEAN DEFAULT FALSE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

INSERT INTO juegos (nombre, descripcion, categoria, materiales, min_jugadores, es_para_beber) VALUES
('Yo Nunca Nunca', 'Un clásico juego de fiesta donde los participantes dicen algo que nunca han hecho, y si alguien sí lo ha hecho, debe tomar.', 'De preguntas', 'Ninguno', 3, TRUE),
('Adivina la Canción', 'Reproduce el inicio de una canción y los demás deben adivinar el título o el artista. El primero en acertar gana un punto.', 'Musical', 'Dispositivo de audio, lista de canciones', 2, FALSE),
('Pictionary', 'Un jugador dibuja una palabra o frase y su equipo debe adivinarla antes de que se acabe el tiempo.', 'Con elementos', 'Papel, lápiz, temporizador', 4, FALSE);
```
