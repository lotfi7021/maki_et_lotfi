import { useState, useEffect } from 'react';
import api from '../api/axios';

export default function Profile() {
    const [user, setUser] = useState(null);
    const [form, setForm] = useState({
        name: '',
        email: ''
    });
    const [passwordForm, setPasswordForm] = useState({
        current_password: '',
        password: '',
        password_confirmation: ''
    });
    const [loading, setLoading] = useState(true);
    const [updating, setUpdating] = useState(false);
    const [updatingPassword, setUpdatingPassword] = useState(false);
    const [message, setMessage] = useState({ type: '', text: '' });
    const [activeTab, setActiveTab] = useState('profile'); // 'profile' or 'password'

    useEffect(() => {
        fetchProfile();
    }, []);

    const fetchProfile = async () => {
        try {
            const response = await api.get('/user/profile');
            setUser(response.data);
            setForm({
                name: response.data.name,
                email: response.data.email
            });
        } catch (err) {
            if (err.response?.status === 401) {
                window.location.href = '/login';
            }
        } finally {
            setLoading(false);
        }
    };

    const handleProfileChange = (e) => {
        setForm({ ...form, [e.target.name]: e.target.value });
    };

    const handlePasswordChange = (e) => {
        setPasswordForm({ ...passwordForm, [e.target.name]: e.target.value });
    };

    const updateProfile = async (e) => {
        e.preventDefault();
        setUpdating(true);
        setMessage({ type: '', text: '' });

        try {
            const response = await api.put('/user/profile', form);
            setUser(response.data.user);
            setMessage({ type: 'success', text: 'Profil mis à jour avec succès !' });
            setTimeout(() => setMessage({ type: '', text: '' }), 3000);
        } catch (err) {
            if (err.response?.status === 422) {
                const errors = err.response.data.errors;
                const firstError = Object.values(errors)[0]?.[0];
                setMessage({ type: 'error', text: firstError || 'Données invalides' });
            } else {
                setMessage({ type: 'error', text: 'Erreur lors de la mise à jour' });
            }
        } finally {
            setUpdating(false);
        }
    };

    const updatePassword = async (e) => {
        e.preventDefault();
        setUpdatingPassword(true);
        setMessage({ type: '', text: '' });

        if (passwordForm.password !== passwordForm.password_confirmation) {
            setMessage({ type: 'error', text: 'Les nouveaux mots de passe ne correspondent pas' });
            setUpdatingPassword(false);
            return;
        }

        try {
            await api.put('/user/password', {
                current_password: passwordForm.current_password,
                password: passwordForm.password,
                password_confirmation: passwordForm.password_confirmation
            });
            setMessage({ type: 'success', text: 'Mot de passe mis à jour avec succès !' });
            setPasswordForm({
                current_password: '',
                password: '',
                password_confirmation: ''
            });
            setTimeout(() => setMessage({ type: '', text: '' }), 3000);
        } catch (err) {
            if (err.response?.status === 400) {
                setMessage({ type: 'error', text: err.response.data.message });
            } else if (err.response?.status === 422) {
                setMessage({ type: 'error', text: 'Le mot de passe doit faire au moins 6 caractères' });
            } else {
                setMessage({ type: 'error', text: 'Erreur lors du changement de mot de passe' });
            }
        } finally {
            setUpdatingPassword(false);
        }
    };

    const handleLogout = async () => {
        try {
            await api.post('/auth/logout');
        } catch (err) {
            // Ignore les erreurs de logout
        } finally {
            localStorage.removeItem('token');
            localStorage.removeItem('user');
            window.location.href = '/login';
        }
    };

    if (loading) {
        return (
            <div style={styles.container}>
                <div style={styles.card}>
                    <div style={styles.loading}>Chargement...</div>
                </div>
            </div>
        );
    }

    return (
        <div style={styles.container}>
            <div style={styles.card}>
                <div style={styles.header}>
                    <h2 style={styles.title}>Mon Profil</h2>
                    <button onClick={handleLogout} style={styles.logoutButton}>
                        Déconnexion
                    </button>
                </div>

                {/* Afficher le nom de l'utilisateur connecté - utilisation de user */}
                {user && (
                    <div style={styles.userInfo}>
                        <p>Connecté en tant que : <strong>{user.name}</strong> ({user.email})</p>
                    </div>
                )}

                {message.text && (
                    <div style={message.type === 'success' ? styles.success : styles.error}>
                        {message.text}
                    </div>
                )}

                <div style={styles.tabs}>
                    <button
                        onClick={() => setActiveTab('profile')}
                        style={activeTab === 'profile' ? styles.activeTab : styles.tab}
                    >
                        Informations personnelles
                    </button>
                    <button
                        onClick={() => setActiveTab('password')}
                        style={activeTab === 'password' ? styles.activeTab : styles.tab}
                    >
                        Changer mot de passe
                    </button>
                </div>

                {activeTab === 'profile' && (
                    <form onSubmit={updateProfile}>
                        <div style={styles.field}>
                            <label style={styles.label}>Nom</label>
                            <input
                                type="text"
                                name="name"
                                value={form.name}
                                onChange={handleProfileChange}
                                style={styles.input}
                                required
                            />
                        </div>

                        <div style={styles.field}>
                            <label style={styles.label}>Email</label>
                            <input
                                type="email"
                                name="email"
                                value={form.email}
                                onChange={handleProfileChange}
                                style={styles.input}
                                required
                            />
                        </div>

                        <button
                            type="submit"
                            style={updating ? styles.buttonDisabled : styles.button}
                            disabled={updating}
                        >
                            {updating ? 'Mise à jour...' : 'Mettre à jour le profil'}
                        </button>
                    </form>
                )}

                {activeTab === 'password' && (
                    <form onSubmit={updatePassword}>
                        <div style={styles.field}>
                            <label style={styles.label}>Mot de passe actuel</label>
                            <input
                                type="password"
                                name="current_password"
                                value={passwordForm.current_password}
                                onChange={handlePasswordChange}
                                style={styles.input}
                                required
                            />
                        </div>

                        <div style={styles.field}>
                            <label style={styles.label}>Nouveau mot de passe</label>
                            <input
                                type="password"
                                name="password"
                                value={passwordForm.password}
                                onChange={handlePasswordChange}
                                style={styles.input}
                                required
                            />
                        </div>

                        <div style={styles.field}>
                            <label style={styles.label}>Confirmer le nouveau mot de passe</label>
                            <input
                                type="password"
                                name="password_confirmation"
                                value={passwordForm.password_confirmation}
                                onChange={handlePasswordChange}
                                style={styles.input}
                                required
                            />
                        </div>

                        <button
                            type="submit"
                            style={updatingPassword ? styles.buttonDisabled : styles.button}
                            disabled={updatingPassword}
                        >
                            {updatingPassword ? 'Changement...' : 'Changer le mot de passe'}
                        </button>
                    </form>
                )}
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
        padding: '20px',
    },
    card: {
        backgroundColor: '#fff',
        padding: '40px',
        borderRadius: '12px',
        boxShadow: '0 4px 20px rgba(0,0,0,0.1)',
        width: '100%',
        maxWidth: '500px',
    },
    header: {
        display: 'flex',
        justifyContent: 'space-between',
        alignItems: 'center',
        marginBottom: '24px',
    },
    title: {
        color: '#1a1a2e',
        fontSize: '24px',
        margin: 0,
    },
    logoutButton: {
        backgroundColor: '#ef4444',
        color: '#fff',
        border: 'none',
        borderRadius: '6px',
        padding: '8px 16px',
        cursor: 'pointer',
        fontSize: '14px',
    },
    userInfo: {
        backgroundColor: '#f3f4f6',
        padding: '10px',
        borderRadius: '8px',
        marginBottom: '20px',
        fontSize: '14px',
        color: '#374151',
    },
    tabs: {
        display: 'flex',
        gap: '10px',
        marginBottom: '24px',
        borderBottom: '1px solid #e5e7eb',
    },
    tab: {
        padding: '10px 16px',
        backgroundColor: 'transparent',
        border: 'none',
        cursor: 'pointer',
        color: '#6b7280',
        fontSize: '14px',
        transition: 'all 0.2s',
    },
    activeTab: {
        padding: '10px 16px',
        backgroundColor: 'transparent',
        border: 'none',
        cursor: 'pointer',
        color: '#4f46e5',
        fontSize: '14px',
        borderBottom: '2px solid #4f46e5',
        fontWeight: 'bold',
    },
    field: {
        marginBottom: '20px',
    },
    label: {
        display: 'block',
        marginBottom: '8px',
        color: '#374151',
        fontSize: '14px',
        fontWeight: '500',
    },
    input: {
        width: '100%',
        padding: '10px 14px',
        borderRadius: '8px',
        border: '1px solid #d1d5db',
        fontSize: '15px',
        outline: 'none',
        boxSizing: 'border-box',
        transition: 'border-color 0.2s',
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
        transition: 'background-color 0.2s',
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
        padding: '12px',
        borderRadius: '8px',
        marginBottom: '20px',
        fontSize: '14px',
        textAlign: 'center',
    },
    success: {
        backgroundColor: '#dcfce7',
        color: '#16a34a',
        padding: '12px',
        borderRadius: '8px',
        marginBottom: '20px',
        fontSize: '14px',
        textAlign: 'center',
    },
    loading: {
        textAlign: 'center',
        color: '#6b7280',
        padding: '40px',
    },
};