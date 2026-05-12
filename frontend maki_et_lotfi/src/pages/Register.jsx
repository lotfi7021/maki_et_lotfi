import { useState } from 'react';
import api from '../api/axios';

export default function Register() {
    const [form, setForm]       = useState({
        name: '',
        email: '',
        password: '',
        password_confirmation: ''
    });
    const [error, setError]     = useState('');
    const [success, setSuccess] = useState('');
    const [loading, setLoading] = useState(false);

    const handleChange = (e) => {
        setForm({ ...form, [e.target.name]: e.target.value });
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        setLoading(true);
        setError('');
        setSuccess('');

        if (form.password !== form.password_confirmation) {
            setError('Les mots de passe ne correspondent pas');
            setLoading(false);
            return;
        }

        try {
            await api.post('/auth/register', form);
            setSuccess('Compte créé ! Vérifiez votre email avant de vous connecter.');
            setTimeout(() => {
                window.location.href = '/login';
            }, 3000);
        } catch (err) {
            if (err.response?.data?.errors?.email) {
                setError('Cet email est déjà utilisé');
            } else {
                setError("Erreur lors de l'inscription");
            }
        } finally {
            setLoading(false);
        }
    };

    return (
        <div style={styles.container}>
            <div style={styles.card}>
                <h2 style={styles.title}>Inscription</h2>

                {error   && <div style={styles.error}>{error}</div>}
                {success && <div style={styles.success}>{success}</div>}

                <form onSubmit={handleSubmit}>
                    <div style={styles.field}>
                        <label style={styles.label}>Nom</label>
                        <input
                            type="text"
                            name="name"
                            value={form.name}
                            onChange={handleChange}
                            style={styles.input}
                            placeholder="Ahmed Ben Ali"
                            required
                        />
                    </div>

                    <div style={styles.field}>
                        <label style={styles.label}>Email</label>
                        <input
                            type="email"
                            name="email"
                            value={form.email}
                            onChange={handleChange}
                            style={styles.input}
                            placeholder="ahmed@test.com"
                            required
                        />
                    </div>

                    <div style={styles.field}>
                        <label style={styles.label}>Mot de passe</label>
                        <input
                            type="password"
                            name="password"
                            value={form.password}
                            onChange={handleChange}
                            style={styles.input}
                            placeholder="••••••"
                            required
                        />
                    </div>

                    <div style={styles.field}>
                        <label style={styles.label}>Confirmer le mot de passe</label>
                        <input
                            type="password"
                            name="password_confirmation"
                            value={form.password_confirmation}
                            onChange={handleChange}
                            style={styles.input}
                            placeholder="••••••"
                            required
                        />
                    </div>

                    <button
                        type="submit"
                        style={loading ? styles.buttonDisabled : styles.button}
                        disabled={loading}
                    >
                        {loading ? 'Inscription...' : "S'inscrire"}
                    </button>
                </form>

                <p style={styles.footer}>
                    Déjà un compte ? <a href="/login" style={styles.link}>Se connecter</a>
                </p>
            </div>
        </div>
    );
}

const styles = {
    container: {
        minHeight: '100vh',
        display: 'flex',
        alignItems: 'center',
        justifyContent: 'center',
        backgroundColor: '#f0f2f5',
    },
    card: {
        backgroundColor: '#fff',
        padding: '40px',
        borderRadius: '12px',
        boxShadow: '0 4px 20px rgba(0,0,0,0.1)',
        width: '100%',
        maxWidth: '400px',
    },
    title: {
        textAlign: 'center',
        marginBottom: '24px',
        color: '#1a1a2e',
        fontSize: '24px',
    },
    field: {
        marginBottom: '16px',
    },
    label: {
        display: 'block',
        marginBottom: '6px',
        color: '#555',
        fontSize: '14px',
    },
    input: {
        width: '100%',
        padding: '10px 14px',
        borderRadius: '8px',
        border: '1px solid #ddd',
        fontSize: '15px',
        outline: 'none',
        boxSizing: 'border-box',
    },
    button: {
        width: '100%',
        padding: '12px',
        backgroundColor: '#4f46e5',
        color: '#fff',
        border: 'none',
        borderRadius: '8px',
        fontSize: '16px',
        cursor: 'pointer',
        marginTop: '8px',
    },
    buttonDisabled: {
        width: '100%',
        padding: '12px',
        backgroundColor: '#a5b4fc',
        color: '#fff',
        border: 'none',
        borderRadius: '8px',
        fontSize: '16px',
        cursor: 'not-allowed',
        marginTop: '8px',
    },
    error: {
        backgroundColor: '#fee2e2',
        color: '#dc2626',
        padding: '10px',
        borderRadius: '8px',
        marginBottom: '16px',
        fontSize: '14px',
        textAlign: 'center',
    },
    success: {
        backgroundColor: '#dcfce7',
        color: '#16a34a',
        padding: '10px',
        borderRadius: '8px',
        marginBottom: '16px',
        fontSize: '14px',
        textAlign: 'center',
    },
    footer: {
        textAlign: 'center',
        marginTop: '20px',
        color: '#666',
        fontSize: '14px',
    },
    link: {
        color: '#4f46e5',
        textDecoration: 'none',
    },
};