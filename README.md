# Monetizze Test

## Teste prático processo seletivo Monetizze
### Execução

Clone o repositório:

`git clone https://github.com/marconycaldeira/monetizze-test.git .`

Acesse o diretório da aplicação:

`cd monetizze-test`

Agora basta executar o arquivo **index.php** ou se preferir execute

`php -q index.php >> index.html`

e em seguida abra o arquivo criado **index.html**


### Exemplo de uso
```php
<?php

use App\Services\Lottery;

$game = new Lottery(6, 8);
$game->play();
$game->exportResult();
```


Author: Marcony Caldeira

Contato: marconycaldeira@gmail.com
