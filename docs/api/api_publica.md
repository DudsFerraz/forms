# API pública

Use `Uspdev\Forms\Facades\Forms` como porta oficial da biblioteca.

```php
use Uspdev\Forms\Facades\Forms;
```

## Regra de versão

Métodos que recebem `name` e `version` devem aceitar a versão como parâmetro opcional. Quando `version` for omitida, a biblioteca deve usar a versão ativa daquele `name`.

Use versão explícita quando a operação precisar ser reprodutível ou presa a uma definição concreta. Use versão omitida quando o objetivo for sempre trabalhar com a versão ativa.

## Definições

Busque a versão ativa:

```php
$definition = Forms::definition('parecer_final');
```

Busque uma versão explícita:

```php
$definition = Forms::definition('parecer_final', 2);
```

Busque a versão ativa de forma explícita, quando o código precisar deixar essa intenção clara:

```php
$definition = Forms::activeDefinition('parecer_final');
```

Liste definições por grupo:

```php
$definitions = Forms::definitions('workflow');
```

## Renderização

Renderize a versão ativa:

```php
$html = Forms::render('parecer_final', [
    'action' => route('pareceres.store'),
    'method' => 'POST',
]);
```

Renderize uma versão explícita:

```php
$html = Forms::render('parecer_final', 2, [
    'action' => route('pareceres.store'),
    'method' => 'POST',
]);
```

Para edição, passe a submissão. A renderização deve usar a definição relacionada à submissão, mesmo que uma versão ativa mais recente exista.

```php
$html = Forms::render('parecer_final', ['method' => 'PUT'], $submission);
```

## Submissões

```php
$submission = Forms::submit($request);
$submission = Forms::update($request, $submission);
$submission = Forms::submission($id);
$submissions = Forms::submissions('parecer_final', key: 'workflow-123');
$submissions = Forms::submissions('parecer_final', 2, 'workflow-123');
```

`submit` e `update` devem retornar `FormSubmission` ou lançar exceção, como `ValidationException`. A API pública não deve retornar strings ou arrays de erro legados.

## Uso sem persistência

A biblioteca também pode ser usada apenas para renderizar e validar formulários, sem persistir os dados submetidos em `form_submissions`.

Esse modo é indicado quando a aplicação quer usar o `forms` como componente de interface e validação, mas precisa processar os dados por conta própria, por exemplo:

* enviar os dados para outra API;
* alimentar uma regra de negócio própria;
* salvar em uma tabela específica da aplicação;
* usar os dados dentro de uma transição de workflow sem criar uma submissão persistida.

Renderize normalmente:

```php
$html = Forms::render('parecer_final', [
    'action' => route('parecer.preview'),
    'method' => 'POST',
]);
```

Valide sem persistir:

```php
$validated = Forms::validate($request);
```

Nesse formato, a biblioteca deve resolver a definição a partir dos dados do request. Se o request não trouxer a identificação do formulário, informe `name` e, se necessário, `version`.

Com nome e versão ativa:

```php
$validated = Forms::validate($request, 'parecer_final');
```

Com nome e versão explícita:

```php
$validated = Forms::validate($request, 'parecer_final', 1);
```

`Forms::validate()` deve retornar os dados validados ou lançar `ValidationException`. Ele não deve criar nem atualizar `FormSubmission`.

Use `Forms::submit()` ou `Forms::update()` somente quando a intenção for persistir em `form_submissions`.

## Filtros

```php
$submissions = Forms::filterSubmissions(
    'parecer_final',
    field: 'resultado',
    operator: '==',
    value: 'aprovado',
    key: 'workflow-123'
);
```

Com versão explícita:

```php
$submissions = Forms::filterSubmissions(
    'parecer_final',
    2,
    'resultado',
    '==',
    'aprovado',
    'workflow-123'
);
```

### Operadores suportados

| Operador | Significado |
| -------- | ----------- |
| `contains` | busca se o valor informado está contido no campo JSON |
| `==` | igualdade |
| `!=` | diferença simples |
| `empty` | campo nulo ou string vazia |
| `not_empty` | campo não nulo e diferente de string vazia |

Use `==` para comparação de igualdade. O operador `=` não é aceito pela API pública.

## Arquivos

```php
return Forms::downloadFile($submission, 'arquivo');
```

## Exclusão

```php
$deleted = Forms::deleteSubmission($submission, auth()->user());
```

## Sincronização

```php
$result = Forms::syncFromDirectory(storage_path('app/formsJson'));
```

`syncFromDirectory` lê arquivos `.json` de um diretório e sincroniza as definições com a tabela `form_definitions`.

O método deve:

* ler apenas arquivos JSON do diretório informado;
* validar cada definição com `FormDefinitionSchemaValidator`;
* criar ou atualizar registros usando `name + version`;
* se um JSON vier com `is_active = true`, desativar as outras versões do mesmo `name`;
* retornar um resumo com arquivos processados, criados, atualizados, ignorados e erros.

Esse método é útil para manter definições versionadas em arquivos do projeto e publicá-las no banco durante deploy, setup local ou atualização controlada de ambientes.
