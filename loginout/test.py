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

# Main project window and planner window
def start_project():
    def create_gui():
        planner = tk.Toplevel(root)
        planner.title("Planificateur de Révisions")
        planner.geometry("900x700")
        planner.configure(bg="#2b3e50")

        tk.Label(planner, text="Matières (séparées par des virgules):", fg="white", bg="#2b3e50").grid(row=0, column=0, padx=10)
        matières_entry = tk.Entry(planner, width=50)
        matières_entry.grid(row=0, column=1, pady=5)

        tk.Label(planner, text="Heures disponibles:", fg="white", bg="#2b3e50").grid(row=1, column=0, padx=10)
        heures_entry = tk.Entry(planner, width=50)
        heures_entry.grid(row=1, column=1, pady=5)

        tk.Label(planner, text="Priorités (1=haute, 3=basse):", fg="white", bg="#2b3e50").grid(row=2, column=0, padx=10)
        priorité_frame = tk.Frame(planner, bg="#2b3e50")
        priorité_frame.grid(row=3, column=0, columnspan=2, pady=10)

        tk.Label(planner, text="Date de début:", fg="white", bg="#2b3e50").grid(row=4, column=0, padx=10)
        date_picker = DateEntry(planner, width=15, background="darkblue", foreground="white", date_pattern="yyyy-MM-dd")
        date_picker.grid(row=4, column=1, pady=5)

        tk.Label(planner, text="Heure de début (HH:MM):", fg="white", bg="#2b3e50").grid(row=5, column=0, padx=10)
        time_entry = tk.Entry(planner, width=10)
        time_entry.insert(0, "08:00")
        time_entry.grid(row=5, column=1, pady=5, sticky=tk.W)

        def generate_priorities():
            for widget in priorité_frame.winfo_children():
                widget.destroy()
            matières = [m.strip() for m in matières_entry.get().split(",")]
            priorité_inputs.clear()
            for matière in matières:
                row = tk.Frame(priorité_frame, bg="#2b3e50")
                row.pack(anchor=tk.W, pady=2)
                tk.Label(row, text=matière, fg="white", bg="#2b3e50", width=15).pack(side=tk.LEFT)
                priorité_input = tk.Entry(row, width=5)
                priorité_input.pack(side=tk.LEFT)
                priorité_inputs[matière] = priorité_input

        priorité_inputs = {}
        tk.Button(planner, text="Générer Priorités", command=generate_priorities, bg="#4CAF50", fg="white").grid(row=3, column=1, pady=5)

        def show_results(plan):
            result_window = tk.Toplevel(planner)
            result_window.title("Résultats de la Planification")
            result_window.geometry("600x400")
            result_window.configure(bg="#34495e")
            tk.Label(result_window, text="Résultats de votre Emploi du Temps", font=("Helvetica", 16), fg="white", bg="#34495e").pack(pady=10)

            columns = ("date", "time", "activity", "subject")
            plan_tree = ttk.Treeview(result_window, columns=columns, show="headings", height=15)
            plan_tree.pack(fill=tk.BOTH, expand=True, padx=10, pady=10)

            plan_tree.heading("date", text="Date")
            plan_tree.heading("time", text="Heure")
            plan_tree.heading("activity", text="Activité")
            plan_tree.heading("subject", text="Matière")

            for entry in plan:
                plan_tree.insert('', 'end', values=entry)

            tk.Button(result_window, text="Recommencer", bg="#4CAF50", fg="white", command=start_project).pack(pady=10)

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

                show_results(emploi_du_temps)
            except Exception as e:
                messagebox.showerror("Erreur", f"Erreur : {e}")

        tk.Button(planner, text="Planifier", bg="#4CAF50", fg="white", command=calculate_plan).grid(row=6, column=0, columnspan=2, pady=10)

    create_gui()

# Main window
root = tk.Tk()
root.title("Bienvenue au Projet")
root.geometry("400x200")
root.configure(bg="#2b3e50")

emoji_bot = "\U0001F916"
tk.Label(root, text=f"{emoji_bot} Bonjour! Bienvenue dans le projet!", font=("Helvetica", 16), fg="white", bg="#2b3e50").pack(pady=20)
tk.Button(root, text="Commencer", font=("Helvetica", 14), bg="#4CAF50", fg="white", command=start_project).pack(pady=10)

root.mainloop()
