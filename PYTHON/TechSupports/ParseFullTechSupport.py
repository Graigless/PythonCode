from tkinter.filedialog import askopenfilename
#import askopenfilename

filename = askopenfilename(initialdir = "/Users/gyankou/Desktop/",title = "Select a full tech support file")
fp = open(filename, 'r')

outf = open('/Users/gyankou/Desktop/Prox_TS/output_file5.txt', 'w')

lines = fp.readlines()

for row in lines:
    word = 'Tech Support SubSection =  "service usage"'
    if  row.find(word) != -1:
        row2start = lines.index(row)

for row in lines:
    word2 = 'Number of trunk group users in the system'
    if  row.find(word2) != -1:
        row2end = lines.index(row)

#end = len(lines)
for out_l in lines[row2start+4:row2end+1]:
    outf.write(out_l)

fp.close()
outf.close()