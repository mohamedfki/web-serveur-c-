#include <QApplication>
#include <QWidget>
#include <QPushButton>
#include <QLineEdit>
#include <QFormLayout>
#include <QFile>
#include <QTextStream>
#include <QMessageBox>
#include <QFileDialog>

class MainWindow : public QWidget {
    Q_OBJECT

public:
    MainWindow(QWidget *parent = nullptr) : QWidget(parent) {
        // Create UI elements
        cinEdit = new QLineEdit(this);
        nomEdit = new QLineEdit(this);
        prenomEdit = new QLineEdit(this);
        emailEdit = new QLineEdit(this);
        telephoneEdit = new QLineEdit(this);
        QPushButton *ajouterButton = new QPushButton("Ajouter", this);
        QPushButton *exporterButton = new QPushButton("Exporter", this);

        // Create layout and add UI elements
        QFormLayout *layout = new QFormLayout();
        layout->addRow("CIN", cinEdit);
        layout->addRow("Nom", nomEdit);
        layout->addRow("Prénom", prenomEdit);
        layout->addRow("Email", emailEdit);
        layout->addRow("Téléphone", telephoneEdit);
        layout->addWidget(ajouterButton);
        layout->addWidget(exporterButton);

        // Set layout to the window
        setLayout(layout);

        // Connect button click signals to the slots
        connect(ajouterButton, &QPushButton::clicked, this, &MainWindow::onAjouterButtonClicked);
        connect(exporterButton, &QPushButton::clicked, this, &MainWindow::onExporterButtonClicked);
    }

private slots:
    void onAjouterButtonClicked() {
        QString cin = cinEdit->text();
        QString nom = nomEdit->text();
        QString prenom = prenomEdit->text();
        QString email = emailEdit->text();
        QString telephone = telephoneEdit->text();
        QString filename = "etudiants.csv";

        if (ajouterEtudiant(filename, cin, nom, prenom, email, telephone)) {
            QMessageBox::information(this, "Succès", "Etudiant ajouté avec succès.");
        } else {
            QMessageBox::warning(this, "Erreur", "Impossible d'ouvrir le fichier.");
        }
    }

    void onExporterButtonClicked() {
        QString sourceFilename = "etudiants.csv";
        QString targetFilename = QFileDialog::getSaveFileName(this, "Exporter le fichier CSV", "", "Fichiers CSV (*.csv)");

        if (!targetFilename.isEmpty()) {
            if (QFile::copy(sourceFilename, targetFilename)) {
                QMessageBox::information(this, "Succès", "Fichier exporté avec succès.");
            } else {
                QMessageBox::warning(this, "Erreur", "Impossible d'exporter le fichier.");
            }
        }
    }

private:
    QLineEdit *cinEdit;
    QLineEdit *nomEdit;
    QLineEdit *prenomEdit;
    QLineEdit *emailEdit;
    QLineEdit *telephoneEdit;

    bool ajouterEtudiant(const QString &filename, const QString &cin, const QString &nom, const QString &prenom, const QString &email, const QString &telephone) {
        QFile file(filename);
        if (file.open(QIODevice::Append | QIODevice::Text)) {
            QTextStream out(&file);
            out << cin << "," << nom << "," << prenom << "," << email << "," << telephone << "\n";
            file.close();
            return true;
        }
        return false;
    }
};

int main(int argc, char *argv[]) {
    QApplication app(argc, argv);

    MainWindow window;
    window.setWindowTitle("Gestion des Étudiants");
    window.resize(300, 200);
    window.show();

    return app.exec();
}

#include "main.moc"
