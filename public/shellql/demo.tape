# ShellQL demo — for fissible.dev /tools/shellql
# Renders with: vhs demo.tape

Output demo.gif

Set Shell "bash"
Set FontSize 14
Set Width 1200
Set Height 700
Set Theme "Dracula"
Set TypingSpeed 80ms
Set PlaybackSpeed 0.8

# Launch ShellQL with demo database
Type "SHQL_THEME=fission shql /tmp/demo.db"
Sleep 300ms
Enter
Sleep 3s

# Pause on the welcome/connections screen so viewer can read it
Sleep 1s

# Navigate down to select the database and open it
Down
Sleep 200ms
Enter
Sleep 2s

# Now we're in the table browser — products table is shown
# Move down a couple rows
Down
Sleep 400ms
Down
Sleep 400ms

# Open the row inspector (Enter)
Enter
Sleep 1.5s

# Close inspector and go back to table
Escape
Sleep 500ms

# Open filter
Type "f"
Sleep 1s

# Focus is on column field — type column name
Type "name"
Sleep 400ms

# Tab to operator field, cycle right 6x to reach LIKE
Tab
Sleep 300ms
Right
Sleep 150ms
Right
Sleep 150ms
Right
Sleep 150ms
Right
Sleep 150ms
Right
Sleep 150ms
Right
Sleep 300ms

# Tab to value field
Tab
Sleep 300ms

# Type LIKE pattern
Type "%Wildflower%"
Sleep 500ms
Enter
Sleep 1.5s

# Clear filter and quit
Escape
Sleep 500ms
Type "q"
Sleep 500ms
