import re

with open(r'c:\Projetos\ProjetoAcademia\laravel-app\resources\views\partials\sidebar.blade.php', 'r', encoding='utf-8') as f:
    lines = f.readlines()

stack = []
for i, line in enumerate(lines):
    line_num = i + 1
    # Find all blade directives
    directives = re.findall(r'@(if|elseif|else|endif|foreach|endforeach|php|endphp)', line)
    for d in directives:
        if d == 'if' or d == 'foreach' or d == 'php':
            stack.append((d, line_num))
        elif d == 'endif':
            if not stack or stack[-1][0] != 'if':
                print(f"Error: unmatched @endif at line {line_num}")
            else:
                stack.pop()
        elif d == 'endforeach':
            if not stack or stack[-1][0] != 'foreach':
                print(f"Error: unmatched @endforeach at line {line_num}. Current stack: {stack}")
            else:
                stack.pop()
        elif d == 'endphp':
            if not stack or stack[-1][0] != 'php':
                print(f"Error: unmatched @endphp at line {line_num}")
            else:
                stack.pop()

if stack:
    print(f"Error: unclosed directives: {stack}")
else:
    print("No obvious nesting issues found.")
