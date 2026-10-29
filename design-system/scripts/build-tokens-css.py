import json,sys
T=json.load(open(sys.argv[1])); out=[f"/* {T['name']} — generated from tokens.json */"]
th=[t['id'] for t in T['color']['themes']]; first=th[0]
def val(v,t):
    if isinstance(v,str): return v if t==first else None
    return v.get(t)
def conv(x): return 'var(--'+x[1:-1]+')' if x.startswith('{') else x
for t in th:
    sel=f':root, [data-theme="{t}"]' if t==first else f'[data-theme="{t}"]'
    lines=[]
    for fam in ('color','shadow'):
        for k in T.get(fam,{}).get('tokens',[]):
            v=val(k['value'],t)
            if v: lines.append(f"  --{k['name']}: {conv(v)};")
    if t!=first: lines.append('  color-scheme: dark;')
    out.append(sel+' {\n'+'\n'.join(lines)+'\n}')
lines=[]
for fam in ('spacing','radius','zIndex'):
    for k in T[fam]['tokens']: lines.append(f"  --{k['name']}: {k['value']};")
for k,v in T['type']['families'].items(): lines.append(f"  --font-{k}: {v};")
out.append(':root {\n'+'\n'.join(lines)+'\n}')
for g in T['type']['groups']:
    for s in g['styles']:
        lh=s['lineHeight']; props=[f"font-family: var(--font-{s.get('family',g['family'])})",f"font-size: {s['fontSize']}",f"line-height: {lh}",f"font-weight: {s['fontWeight']}"]
        if 'letterSpacing' in s: props.append(f"letter-spacing: {s['letterSpacing']}")
        if s['name']=='overline': props.append('text-transform: uppercase')
        out.append(f".{s['name']} {{ "+'; '.join(props)+'; }')
print('\n'.join(out))
