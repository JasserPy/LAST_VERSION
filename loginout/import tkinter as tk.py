import tkinter as tk
from tkinter import ttk, messagebox
from tkcalendar import DateEntry
from datetime import datetime, timedelta

# Planification functions
def planifier_revisions(matières, heures_disponibles, priorités):
    matières_triées = sorted(matières, key=lambda x: priorités[x])
    heures_par_matière = heures_disponibles // len(matières)
    plan = {matière: heures_par_matière for matière in matières_triées}
    heures_restantes = heures_disponibles % len(matières)
    for matière in matières_triées:
        if heures_restantes > 0:
            plan[matière] += 1
            heures_restantes -= 1
    return plan

def ajuster_avec_pauses(plan):
    plan_avec_pauses = {}
    for matière, heures in plan.items():
        heures_avec_pauses = heures + (heures // 2)  # adding breaks after every hour
        plan_avec_pauses[matière] = heures_avec_pauses
    return plan_avec_pauses

def generer_emploi_du_temps(plan, date_debut):
    emploi_du_temps = []
    current_time = date_debut
    for matière, heures in plan.items():
        for heure in range(heures):
            if (heure + 1) % 3 == 0:
                emploi_du_temps.append((current_time.strftime('%Y-%m-%d'), current_time.strftime('%H:%M'), "Pause", ""))
            else:
                emploi_du_temps.append((current_time.strftime('%Y-%m-%d'), current_time.strftime('%H:%M'), "Révision", matière))
            current_time += timedelta(hours=1)
    return emploi_du_temps

# Animation Functions
def animate_color_change(widget, start_color, end_color, duration=1000):
    start_rgb = widget.winfo_rgb(start_color)
    end_rgb = widget.winfo_rgb(end_color)
    steps = 100
    step_delay = duration / steps

    def update_color(step):
        r = int(start_rgb[0] + (end_rgb[0] - start_rgb[0]) * step / steps)
        g = int(start_rgb[1] + (end_rgb[1] - start_rgb[1]) * step / steps)
        b = int(start_rgb[2] + (end_rgb[2] - start_rgb[2]) * step / steps)
        widget.configure(bg=f'#{r:04x}{g:04x}{b:04x}')
        if step < steps:
            widget.after(int(step_delay), update_color, step + 1)

    update_color(0)

def fade_in_text(label, text, duration=1000):
    label.configure(text=text)
    steps = 100
    step_delay = duration / steps

    def update_opacity(step):
        opacity = int(255 * step / steps)
        hex_opacity = f'#{opacity:02x}{opacity:02x}{opacity:02x}'
        label.configure(fg=hex_opacity)
        if step < steps:
            label.after(int(step_delay), update_opacity, step + 1)

    update_opacity(0)

def on_button_hover(event):
    event.widget.configure(bg="#45a049")

def on_button_leave(event):
    event.widget.configure(bg="#4CAF50")

# Main project window and planner window
def start_project():
    def create_gui():
        planner = tk.Toplevel(root)
        
        planner.title("Planificateur de Révisions")
        planner.geometry("900x700")
        planner.configure(bg="#2b3e50")

        # Animate background color change
        animate_color_change(planner, "#2b3e50", "#34495e", 2000)  # Transition from dark to lighter background
        emoji_bot = "\U0001F916"
        tk.Label(planner, text=f"Planificateur de Révisions  {emoji_bot}", font=("Helvetica", 18), fg="white", bg="#2b3e50").grid(row=0, column=0, columnspan=2, pady=10)

        # Fade-in text for the welcome label
        welcome_label = tk.Label(planner, text="Bienvenue! Commencez à planifier vos révisions.", fg="white", bg="#2b3e50", font=("Helvetica", 14))
        welcome_label.grid(row=1, column=0, columnspan=2, pady=20)
        fade_in_text(welcome_label, "Bienvenue! Commencez à planifier vos révisions.", 3000)

        tk.Label(planner, text="Matières (séparées par des virgules):", fg="white", bg="#2b3e50").grid(row=2, column=0, sticky=tk.W, padx=10)
        matières_entry = tk.Entry(planner, width=40)
        matières_entry.grid(row=2, column=1, pady=5, padx=10)

        tk.Label(planner, text="Heures disponibles:", fg="white", bg="#2b3e50").grid(row=3, column=0, sticky=tk.W, padx=10)
        heures_entry = tk.Entry(planner, width=40)
        heures_entry.grid(row=3, column=1, pady=5, padx=10)

        priorité_frame = tk.Frame(planner, bg="#2b3e50")
        priorité_frame.grid(row=4, column=0, columnspan=2, pady=10)

        tk.Label(priorité_frame, text="Priorités (1=haute, 3=basse):", fg="white", bg="#2b3e50").pack(anchor=tk.W)

        priorité_inputs = {}

        def add_priorité_inputs():
            for widget in priorité_frame.winfo_children():
                if widget != priorité_frame.children['!label']:
                    widget.destroy()
            matières = [m.strip() for m in matières_entry.get().split(",")]
            for matière in matières:
                row = tk.Frame(priorité_frame, bg="#2b3e50")
                row.pack(anchor=tk.W)
                tk.Label(row, text=matière, fg="white", bg="#2b3e50", width=20).pack(side=tk.LEFT)
                priorité_input = tk.Entry(row, width=5)
                priorité_input.pack(side=tk.LEFT)
                priorité_inputs[matière] = priorité_input

        tk.Button(planner, text="Générer Champs de Priorités", command=add_priorité_inputs).grid(row=5, column=0, columnspan=2, pady=10)

        tk.Label(planner, text="Date de début:", fg="white", bg="#2b3e50").grid(row=6, column=0, sticky=tk.W, padx=10)
        date_picker = DateEntry(planner, width=15, background="darkblue", foreground="white", date_pattern="yyyy-MM-dd")
        date_picker.grid(row=6, column=1, pady=5, padx=10, sticky=tk.W)

        tk.Label(planner, text="Heure de début (HH:MM):", fg="white", bg="#2b3e50").grid(row=7, column=0, sticky=tk.W, padx=10)
        time_entry = tk.Entry(planner, width=10)
        time_entry.insert(0, "08:00")
        time_entry.grid(row=7, column=1, pady=5, padx=10, sticky=tk.W)

        columns = ("date", "time", "activity", "subject")
        plan_tree = ttk.Treeview(planner, columns=columns, show="headings", height=20)
        plan_tree.grid(row=8, column=0, columnspan=2, pady=20)

        plan_tree.heading("date", text="Date", anchor=tk.W)
        plan_tree.column("date", width=150, anchor=tk.W)

        plan_tree.heading("time", text="Heure", anchor=tk.W)
        plan_tree.column("time", width=100, anchor=tk.W)

        plan_tree.heading("activity", text="Activité", anchor=tk.W)
        plan_tree.column("activity", width=250, anchor=tk.W)

        plan_tree.heading("subject", text="Matière", anchor=tk.W)
        plan_tree.column("subject", width=250, anchor=tk.W)

        def calculate_plan():
            try:
                matières = [m.strip() for m in matières_entry.get().split(",")]
                heures_disponibles = int(heures_entry.get())
                priorités = {matière: int(priorité_inputs[matière].get()) for matière in matières}
                date_debut_str = date_picker.get() + " " + time_entry.get()
                date_debut = datetime.strptime(date_debut_str, "%Y-%m-%d %H:%M")

                plan = planifier_revisions(matières, heures_disponibles, priorités)
                plan_avec_pauses = ajuster_avec_pauses(plan)
                emploi_du_temps = generer_emploi_du_temps(plan_avec_pauses, date_debut)

                for item in plan_tree.get_children():
                    plan_tree.delete(item)

                for entry in emploi_du_temps:
                    plan_tree.insert('', 'end', values=entry)

            except Exception as e:
                messagebox.showerror("Erreur", f"Une erreur est survenue: {e}")

        # Planifier Button with hover animation
        planifier_button = tk.Button(planner, text="Planifier", command=calculate_plan, bg="#4CAF50", fg="white", font=("Helvetica", 14), width=10)
        planifier_button.grid(row=4, column=2, columnspan=2, pady=20)
        planifier_button.bind("<Enter>", on_button_hover)
        planifier_button.bind("<Leave>", on_button_leave)

    create_gui()

def goodbye():
    messagebox.showinfo("Goodbye", "Au revoir! Passe une bonne journée! 👋")
    root.destroy()

# Main window
root = tk.Tk()
root.title("Bienvenue au Projet")
root.geometry("500x300")
root.configure(bg="#2b3e50")

emoji_bot = "\U0001F916"
tk.Label(root, text=f"{emoji_bot} Bonjour! Bienvenue dans le projet!", font=("Helvetica", 16), fg="white", bg="#2b3e50").pack(pady=20)
tk.Label(root, text="Voulez-vous commencer?", font=("Helvetica", 14), fg="white", bg="#2b3e50").pack(pady=10)

button_frame = tk.Frame(root, bg="#2b3e50")
button_frame.pack()

tk.Button(button_frame, text="Oui", font=("Helvetica", 14), command=start_project, bg="#4CAF50", fg="white", width=10).grid(row=0, column=0, padx=10, pady=10)
tk.Button(button_frame, text="Non", font=("Helvetica", 14), command=goodbye, bg="#F44336", fg="white", width=10).grid(row=0, column=1, padx=10, pady=10)

root.mainloop()
