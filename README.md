# Ingus

Ingus is named after the famous Russian border dog. This is a simple bot for Telegram group spam protection.

## Features

- Detects and removes spam messages
- Easy to configure and use
- Lightweight and efficient

## Installation

1. Clone the repository:
    ```bash
    git clone https://github.com/nikitaglobal/ng-ingus.git
    ```
2. Navigate to the project directory:
    ```bash
    cd ng-ingus
    ```
3. Install the required dependencies:
    ```bash
    composer install
    ```

## Configuration Constants (config.php)
1. NGING_BOT_TOKEN
- Description: This constant holds the token for the bot. It is used to authenticate the bot with the service it is interacting with.
- Required Value: A string representing the bot token. Example: `'81097451760:AAHbtLxD2WgcSXnoA2okkXQqj2-t3uKz0Bk'`.
2. NGINS_CHATTS
- Description: This constant is an array that contains chat IDs. These IDs represent the chats where the bot will be active.
- Required Value: An array of integers. Example: `array( -4709792227 )`.
3. NGINS_ADMIN_CHAT
- Description: This constant holds the chat ID of the admin. It is used to identify the admin chat for administrative purposes.
- Required Value: An integer representing the admin chat ID. Example: `98797678`.
4. NGINS_COPY_ALL
- Description: This constant is a boolean flag that indicates whether the bot should copy all messages to the administrator.
- Required Value: A boolean value (true or false). Example: true.

### Example configuration
```php
<?php
define( 'NGING_BOT_TOKEN', 'your-bot-token-here' );
define( 'NGINS_CHATTS', array( your-chat-id-here ) );
define( 'NGINS_ADMIN_CHAT', your-admin-chat-id-here );
define( 'NGINS_COPY_ALL', true );
```
## Usage

1. Create a new bot on Telegram and get the API token.
2. Set the API token in the configuration file.
3. Run the bot:
    ```bash
    php ingus.php
    ```

## Contributing

Contributions are welcome! Please open an issue or submit a pull request.

## License

This project is licensed under the MIT License.

Maintained by [Nikita Global](https://nikita.global)