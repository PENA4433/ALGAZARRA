# Funcionalidade de Adicionar Crianças - Resumo Técnico

## Visão Geral

Os encarregados de educação podem adicionar novas crianças ao sistema através de uma interface simples e segura. Esta funcionalidade permite que os encarregados gerem completamente o perfil dos seus educandos.

---

## Arquivos Principais

### 1. `criar_perfil.php`
**Descrição:** Página de formulário para adicionar uma nova criança.

**Funcionalidades:**
- Formulário com campos para nome e data de nascimento
- Validação de data (não permite datas futuras)
- Mensagens de erro e sucesso
- Integração no menu de encarregados
- Design responsivo com Bootstrap

**Campos do Formulário:**
- **Nome completo** (obrigatório, 2-100 caracteres)
- **Data de nascimento** (obrigatório, máximo 16 anos)

**Validações no Cliente:**
- Campo de data com `max="<?php echo date('Y-m-d'); ?>"` (impede datas futuras)

### 2. `load_criar_perfil.php`
**Descrição:** Script de processamento que valida e insere a criança na base de dados.

**Funcionalidades:**
- Validação de autenticação (apenas encarregados nível 2)
- Validação de campos obrigatórios
- Validação de comprimento do nome (2-100 caracteres)
- Validação de formato de data
- **Validação de idade: máximo 16 anos**
- Verificação de propriedade do encarregado
- Inserção segura com prepared statements
- Tratamento de erros com mensagens descritivas

**Fluxo de Processamento:**
```
Formulário enviado
    ↓
Validar autenticação
    ↓
Validar campos obrigatórios
    ↓
Validar nome (2-100 caracteres)
    ↓
Validar data de nascimento
    ↓
Validar idade (máximo 16 anos)
    ↓
Verificar propriedade do encarregado
    ↓
Inserir na base de dados
    ↓
Redirecionar com mensagem de sucesso
```

---

## Validações de Segurança

### 1. Autenticação
```php
if (!isset($_SESSION['id_user']) || $_SESSION['nivel'] != 2) {
    include './erro.php';
    exit;
}
```
- Apenas encarregados (nível 2) podem aceder
- Utilizadores não autenticados são redirecionados para erro

### 2. Validação de Propriedade
```php
$stmt_verify = mysqli_prepare($conn, 
    "SELECT id FROM enc_educacao WHERE id = ? 
     AND email = (SELECT email FROM utilizador WHERE id = ?)");
```
- Garante que o encarregado pertence ao utilizador autenticado
- Previne que um encarregado adicione crianças a outro encarregado

### 3. Prepared Statements
```php
$stmt_insert = mysqli_prepare($conn, 
    "INSERT INTO aluno (nome, data_nascimento, enc_educacao) 
     VALUES (?, ?, ?)");
mysqli_stmt_bind_param($stmt_insert, "ssi", $nome, $data_nascimento, $enc_educacao);
```
- Protege contra SQL injection
- Todos os parâmetros são vinculados de forma segura

### 4. Sanitização de Output
```php
echo htmlspecialchars($_SESSION['info']);
```
- Previne XSS attacks
- Todos os dados exibidos são escapados

---

## Validações de Negócio

### Validação de Nome
**Regra:** Nome deve ter entre 2 e 100 caracteres.

**Implementação:**
```php
if (strlen($nome) < 2 || strlen($nome) > 100) {
    $_SESSION['err'] = 'O nome deve ter entre 2 e 100 caracteres.';
    header("Location: criar_perfil.php");
    exit;
}
```

### Validação de Data
**Regra:** Data não pode ser no futuro.

**Implementação:**
```php
$data_obj = DateTime::createFromFormat('Y-m-d', $data_nascimento);
if (!$data_obj) {
    $_SESSION['err'] = 'Data de nascimento inválida.';
    header("Location: criar_perfil.php");
    exit;
}

$hoje = new DateTime();
if ($data_obj > $hoje) {
    $_SESSION['err'] = 'A data de nascimento não pode ser no futuro.';
    header("Location: criar_perfil.php");
    exit;
}
```

### Validação de Idade
**Regra:** Criança deve ter no máximo 16 anos.

**Implementação:**
```php
$idade = $hoje->diff($data_obj)->y;
if ($idade > 16) {
    $_SESSION['err'] = 'A criança deve ter no máximo 16 anos.';
    header("Location: criar_perfil.php");
    exit;
}
```

---

## Integração no Menu

### Menu de Encarregados
A funcionalidade está integrada em três locais:

1. **index.php** - Menu principal
```
Gestão (dropdown)
├── Gerir crianças
├── Inscrever aluno
└── Adicionar criança ← NOVO
```

2. **gerir_criancas.php** - Menu de gestão
```
Gestão (dropdown)
├── Gerir crianças
├── Inscrever aluno
└── Adicionar criança ← NOVO
```

3. **criar_perfil.php** - Menu da própria página
```
Gestão (dropdown)
├── Gerir crianças
├── Inscrever aluno
└── Adicionar criança ← NOVO
```

---

## Fluxo de Dados

### Tabelas Envolvidas
- **utilizador** - Autenticação do encarregado
- **enc_educacao** - Dados do encarregado
- **aluno** - Dados da criança (inserção)

### Relacionamento
```
utilizador (nível 2) ← corresponde a → enc_educacao
enc_educacao ← tem muitos → aluno (nova criança)
```

---

## Testes Recomendados

### Teste 1: Adicionar Criança com Sucesso
1. Login como `encarregado1` (password: `12345`)
2. Aceder a "Gestão" → "Adicionar criança"
3. Preencher:
   - Nome: "João Silva"
   - Data de nascimento: "2010-05-15"
4. Clicar "Adicionar Criança"
5. **Esperado:** Mensagem de sucesso e redirecionamento para gerir_criancas.php

### Teste 2: Rejeição por Idade
1. Tentar adicionar criança com data de nascimento anterior a 2010
2. **Esperado:** Mensagem de erro "A criança deve ter no máximo 16 anos."

### Teste 3: Rejeição por Nome Vazio
1. Deixar nome em branco
2. **Esperado:** Validação HTML5 (campo obrigatório)

### Teste 4: Rejeição por Nome Muito Curto
1. Preencher nome com 1 caractere
2. **Esperado:** Mensagem de erro "O nome deve ter entre 2 e 100 caracteres."

### Teste 5: Rejeição por Data Futura
1. Tentar inserir data de nascimento no futuro
2. **Esperado:** Validação HTML5 (data máxima é hoje) ou mensagem de erro

### Teste 6: Acesso Não Autorizado
1. Tentar aceder a `criar_perfil.php` como professor
2. **Esperado:** Redirecionamento para erro.php

### Teste 7: Verificação de Propriedade
1. Tentar manipular o campo `enc_educacao` para outro encarregado
2. **Esperado:** Mensagem de erro "Encarregado inválido ou não autorizado."

---

## Melhorias Implementadas

### Comparação Antes/Depois

| Aspecto | Antes | Depois |
|--------|-------|--------|
| **Segurança SQL** | `mysqli_real_escape_string()` | Prepared statements |
| **Validação de Idade** | Não havia | Máximo 16 anos |
| **Validação de Nome** | Nenhuma | 2-100 caracteres |
| **Validação de Data** | Nenhuma | Não futuro, formato válido |
| **Menu Integrado** | Não | Sim, em 3 locais |
| **Mensagens de Erro** | Genéricas | Descritivas e específicas |
| **Verificação de Propriedade** | Não | Sim, na base de dados |
| **Sanitização de Output** | Parcial | Completa com htmlspecialchars() |

---

## Dados de Teste

### Encarregado de Teste
| Campo | Valor |
|-------|-------|
| Username | encarregado1 |
| Password | 12345 |
| Email | carlos@gmail.com |
| Nome | Carlos Carvalho |

---

## Conclusão

A funcionalidade de adicionar crianças está **totalmente implementada** com:
- ✅ Interface intuitiva e responsiva
- ✅ Validações robustas de segurança
- ✅ Validações de negócio (idade máxima 16 anos)
- ✅ Prepared statements para proteger contra SQL injection
- ✅ Integração no menu em múltiplos locais
- ✅ Tratamento de erros consistente
- ✅ Sanitização de output para prevenir XSS

Os encarregados podem agora **adicionar, inscrever e gerir completamente as suas crianças** no sistema Algazarra.
