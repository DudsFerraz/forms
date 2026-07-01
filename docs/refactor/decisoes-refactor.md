# Decisões do refactor

Este documento registra decisões arquiteturais tomadas durante o refactor. Ele também documenta alternativas descartadas para evitar que voltem como ambiguidades durante a implementação.

## Forms como API oficial

A facade `Uspdev\Forms\Facades\Forms` é a porta pública oficial da biblioteca. Ela encapsula `FormsService` e evita que consumidores externos dependam da classe interna `Form`.

## Form como implementação interna

`Uspdev\Forms\Form` permanece no pacote para concentrar ou reaproveitar regras existentes de renderização, validação de submissão, upload e download. Ela não deve ser apresentada como API recomendada na documentação pública.

## Versionamento aprovado

`form_definitions` passa a usar `name + version`.

Motivos:

* permitir mais de uma versão concreta para o mesmo formulário lógico;
* preservar a renderização de submissões antigas pela `formDefinition` usada no envio;
* permitir que sistemas consumidores, como `workflow`, referenciem uma versão explícita de formulário em transições.

## Uso da versão ativa quando version é omitida

Chamadas públicas que recebem `name` e `version` podem omitir `version`. Nesse caso, a biblioteca deve usar a versão ativa do formulário.

Essa regra melhora a ergonomia para casos comuns, mas consumidores que precisam de reprodutibilidade devem informar a versão explicitamente.

## Uso sem persistência aprovado

A V2 deve permitir o uso do `forms` sem persistir os dados submetidos.

Esse modo atende aplicações que querem usar a biblioteca como componente de renderização e validação, mas precisam processar os dados fora de `form_submissions`.

Regra definida:

* `Forms::render()` renderiza o formulário.
* `Forms::validate()` valida e retorna os dados validados sem persistir.
* `Forms::submit()` valida e cria `FormSubmission`.
* `Forms::update()` valida e atualiza `FormSubmission`.

## DTOs barrados

DTOs não foram introduzidos neste refactor.

Justificativa:

* a estrutura de `forms` é mais linear que a de um motor de workflow;
* não há grafo de estados, transições, bindings e roles interdependentes;
* DTOs adicionariam cerimônia sem ganho proporcional;
* o ganho real está na validação estrutural centralizada em `FormDefinitionSchemaValidator`.

## form_submission_history barrado por agora

Não foi criada tabela `form_submission_history`.

Justificativa:

* `FormSubmission` já representa o dado submetido;
* a auditoria operacional existente continua usando `spatie/laravel-activitylog`;
* histórico próprio só deve ser reavaliado se houver necessidade de diff estruturado, rollback, snapshots completos por edição ou auditoria independente do Spatie.
