# Funcionalidade de Inscrição de Alunos - Resumo Técnico

## Visão Geral

A plataforma Algazarra implementa um sistema completo de inscrição de alunos em atividades com três níveis de acesso:

1. **Encarregados de Educação** (Nível 2) - Inscrevem apenas os seus alunos
2. **Professores/Administradores** (Nível 1) - Inscrevem alunos de qualquer encarregado
3. **Sistema de Validação** - Garante integridade dos dados

---

## Arquivos Principais

### 1. `inscrever_aluno_encarregado.php`
**Descrição:** Página de inscrição para encarregados de educação.

**Funcionalidades:**
- Apenas utilizadores com nível 2 (encarregados) podem aceder
- Listagem automática dos alunos do encarregado autenticado
- Seleção de atividade disponível
- Validações de segurança e negócio

**Validações Implementadas:**
- ✅ Verificação de autenticação e nível de acesso
- ✅ Validação de propriedade do aluno (aluno pertence ao encarregado)
- ✅ Prevenção de duplicação de inscrições
- ✅ Verificação de lotação máxima da atividade
- ✅ **Validação de idade: rejeita alunos com MAIS de 16 anos**
- ✅ Prepared statements para prevenir SQL injection

**Fluxo de Inscrição:**
```
Encarregado autenticado
    ↓
Seleciona aluno (lista apenas seus alunos)
    ↓
Seleciona atividade
    ↓
Sistema valida todas as regras
    ↓
Inscrição registada com sucesso
```

### 2. `inscrever_aluno_professor.php`
**Descrição:** Página de inscrição para professores/administradores.

**Funcionalidades:**
- Apenas utilizadores com nível 1 (professores) podem aceder
- Seleção de encarregado de educação
- Listagem de alunos do encarregado selecionado
- Seleção de atividade
- Mesmas validações que encarregados

**Fluxo de Inscrição:**
```
Professor autenticado
    ↓
Seleciona encarregado
    ↓
Seleciona aluno do encarregado
    ↓
Seleciona atividade
    ↓
Sistema valida todas as regras
    ↓
Inscrição registada com sucesso
```

### 3. `inscrever.php`
**Descrição:** Página de inscrição rápida para professores (via gerir_criancas.php).

**Funcionalidades:**
- Inscrição direta de um aluno específico
- Listagem de todas as atividades disponíveis
- Mesmas validações de segurança

---

## Validações de Negócio

### Regra de Idade
**Regra:** Alunos com MAIS de 16 anos não podem ser inscritos em atividades.

**Implementação:**
```php
$idade = $hoje->diff($data_nascimento)->y;
if ($idade > 16) {
    $_SESSION['err'] = "O aluno tem mais de 16 anos e não pode ser inscrito.";
    header("Location: inscrever_aluno_encarregado.php");
    exit;
}
```

**Aplicação:** Validação presente em:
- `inscrever_aluno_encarregado.php` (linha 100)
- `inscrever_aluno_professor.php` (linha 69)
- `inscrever.php` (linha 69)

### Verificação de Lotação
**Regra:** Nenhuma atividade pode ter mais inscritos que a sua capacidade máxima.

**Implementação:**
```php
$stmt_lotacao = mysqli_prepare($conn, 
    "SELECT COUNT(*) as total_inscritos, a.lotacao_max 
     FROM inscricao i 
     INNER JOIN atividade a ON i.atividade = a.id 
     WHERE i.atividade = ?
     GROUP BY a.id");
```

### Prevenção de Duplicação
**Regra:** Um aluno não pode ser inscrito duas vezes na mesma atividade.

**Implementação:**
```php
$stmt_check = mysqli_prepare($conn, 
    "SELECT * FROM inscricao WHERE aluno = ? AND atividade = ?");
```

### Validação de Propriedade
**Regra:** Encarregados só podem inscrever os seus próprios alunos.

**Implementação:**
```php
$stmt_aluno = mysqli_prepare($conn, 
    "SELECT id FROM aluno WHERE id = ? AND enc_educacao = ?");
```

---

## Integração no Menu

### Menu de Encarregados (index.php)
A funcionalidade está integrada no menu principal:

```
Gestão (dropdown)
├── Gerir crianças
└── Inscrever aluno ← NOVO
```

**Acesso:** Encarregados autenticados veem este menu no topo da página.

### Menu de Professores (index.php)
```
Gestão (dropdown)
├── Gerir utilizadores
├── Gerir crianças
├── Gerir atividades
├── Criar atividade
└── (Inscrever aluno disponível via inscrever.php)
```

---

## Fluxo de Dados

### Tabelas Envolvidas
- **utilizador** - Autenticação e nível de acesso
- **enc_educacao** - Dados dos encarregados de educação
- **aluno** - Dados dos alunos e relação com encarregado
- **atividade** - Dados das atividades
- **inscricao** - Registos de inscrição

### Relacionamentos
```
utilizador (nível 2) ← corresponde a → enc_educacao
enc_educacao ← tem muitos → aluno
aluno ← inscreve-se em → atividade (através de inscricao)
```

---

## Segurança

### Proteções Implementadas

1. **Prepared Statements**
   - Todas as queries utilizam `mysqli_prepare()` e `mysqli_stmt_bind_param()`
   - Previne SQL injection

2. **Validação de Sessão**
   - Verifica `$_SESSION['id_user']` e `$_SESSION['nivel']`
   - Redireciona utilizadores não autenticados

3. **Controlo de Acesso Baseado em Nível**
   - Nível 1: Professores (acesso total)
   - Nível 2: Encarregados (acesso limitado aos seus alunos)

4. **Sanitização de Output**
   - `htmlspecialchars()` em todos os outputs
   - Previne XSS attacks

5. **Validação de Propriedade**
   - Encarregados só veem/inscrevem os seus alunos
   - Verificação na base de dados, não apenas no cliente

---

## Testes Recomendados

### Teste 1: Encarregado Inscreve Aluno
1. Login como `encarregado1` (password: `12345`)
2. Aceder a "Gestão" → "Inscrever aluno"
3. Selecionar um aluno (ex: Pedro Carvalho - 2002-11-08)
4. Selecionar uma atividade
5. Clicar "Inscrever Aluno"
6. **Esperado:** Mensagem de sucesso

### Teste 2: Rejeição por Idade
1. Tentar inscrever um aluno com mais de 16 anos
2. **Esperado:** Mensagem de erro "O aluno tem mais de 16 anos..."

### Teste 3: Prevenção de Duplicação
1. Inscrever um aluno numa atividade
2. Tentar inscrever o mesmo aluno na mesma atividade novamente
3. **Esperado:** Mensagem de erro "O aluno já está inscrito..."

### Teste 4: Verificação de Lotação
1. Inscrever alunos até atingir a lotação máxima de uma atividade
2. Tentar inscrever mais um aluno
3. **Esperado:** Mensagem de erro "A atividade atingiu o número máximo..."

### Teste 5: Acesso Não Autorizado
1. Tentar aceder a `inscrever_aluno_encarregado.php` como professor
2. **Esperado:** Redirecionamento com mensagem de erro

---

## Dados de Teste

### Encarregados Disponíveis
| ID | Nome | Email | Alunos |
|---|---|---|---|
| 1 | Carlos Carvalho | carlos@gmail.com | Pedro Carvalho (2002-11-08), Miguel Rocha (2002-08-27) |
| 2 | Ana Almeida | ana@gmail.com | Sofia Almeida (2005-03-15), Beatriz Santos (2004-12-05), André Correia (2001-10-03) |
| 3 | João Ferreira | joao@gmail.com | Inês Ferreira (2006-07-22), Carolina Pinto (2005-06-10) |
| 4 | Maria Martins | maria@gmail.com | Tiago Martins (2001-09-30), Leonor Teixeira (2006-04-14) |
| 5 | Paulo Costa | paulo@gmail.com | Rafael Costa (2003-01-19) |

### Atividades Disponíveis
| ID | Título | Datas | Lotação |
|---|---|---|---|
| 1 | Visita ao Estádio da Luz | 2026-07-01 a 2026-07-05 | 50 |
| 2 | Paintball | 2026-07-06 a 2026-07-10 | 40 |
| 3 | Jardim Zoológico de Lisboa | 2026-07-11 a 2026-07-15 | 50 |

---

## Conclusão

A funcionalidade de inscrição de alunos pelos encarregados está **totalmente implementada** com:
- ✅ Interface intuitiva
- ✅ Validações robustas de negócio
- ✅ Proteções de segurança
- ✅ Integração no menu principal
- ✅ Tratamento de erros consistente

O sistema garante que apenas alunos com 16 anos ou menos possam ser inscritos, mantendo a integridade dos dados e a segurança da plataforma.
