# Documentação técnica do Forms

Esta é a porta de entrada da documentação técnica do `uspdev/forms`. Os links abaixo estão organizados na ordem recomendada de leitura para quem precisa entender ou integrar a biblioteca.

## Leitura recomendada

1. [Conceitos e definição de formulário](definicoes/form_definition.md)  
   Entenda como `form_definitions` funciona, como `name + version` identifica uma versão concreta e como a versão ativa é escolhida.

2. [API pública via facade Forms](api/api_publica.md)  
   Consulte os métodos oficiais para renderizar, submeter, atualizar, consultar e sincronizar formulários.

3. [Caso de uso completo: parecer final](casos-de-uso/parecer_final.md)  
   Acompanhe um exemplo de ponta a ponta: definição JSON, sync, renderização, submissão, validação, edição e consulta.

4. [Submissões, auditoria e relacionamento com definições](submissoes/modelagem.md)  
   Veja como `form_submissions` se relaciona com `form_definitions` e por que submissões antigas continuam presas à definição usada no envio.

5. [Validação de form_definition](definicoes/validacao_form_definition.md)  
   Confira as regras aplicadas pelo `FormDefinitionSchemaValidator`.

6. [Guia de migração para consumidores](consumidores/migracao_consumidores.md)  
   Migre sistemas que usam `uspdev/forms`, incluindo bibliotecas consumidoras como `uspdev/workflow`.

7. [Breaking changes](refactor/breaking_changes.md)  
   Veja as mudanças incompatíveis da nova versão.

8. [Decisões do refactor](refactor/decisoes-refactor.md)  
   Consulte as decisões aprovadas e as alternativas descartadas durante o refactor.

## Resumo

`uspdev/forms` é uma biblioteca Laravel para definir formulários dinâmicos, renderizar HTML a partir dessas definições, validar dados com ou sem persistência, consultar dados submetidos e manipular arquivos enviados.

A API pública oficial é a facade `Uspdev\Forms\Facades\Forms`. A classe `Uspdev\Forms\Form` permanece no pacote como implementação interna e não deve ser usada diretamente por sistemas consumidores.

As definições de formulário são identificadas por `name + version`. Quando uma chamada pública omitir `version`, a biblioteca deve usar a versão ativa daquele `name`.

## Divisão de responsabilidades

A separação principal da V2 é entre definição e submissão.

`FormDefinition` representa a estrutura do formulário: nome, versão, versão ativa, grupo, descrição e campos. É a parte que diz como o formulário deve existir e ser validado.

`FormSubmission` representa os dados enviados por alguém. Cada submissão aponta para a definição usada no momento do envio por meio de `form_definition_id`, então uma submissão antiga continua ligada à versão exata do formulário que a criou.

A facade `Forms` é apenas a porta pública da biblioteca. Ela existe para oferecer uma API simples e estável para os consumidores, mas não significa que toda a regra interna precise ficar em uma única classe. A implementação pode ser organizada em serviços internos de definição, submissão, renderização e arquivos, sem mudar o contrato público.
