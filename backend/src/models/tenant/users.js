const Sequelize = require('sequelize');
module.exports = function(sequelize, DataTypes) {
  return sequelize.define('users', {
    id: {
      autoIncrement: true,
      type: DataTypes.INTEGER,
      allowNull: false,
      primaryKey: true
    },
    c_id: {
      type: DataTypes.INTEGER,
      allowNull: false
    },
    user_name: {
      type: DataTypes.STRING(255),
      allowNull: false
    },
    academic_year: {
      type: DataTypes.STRING(50),
      allowNull: false
    },
    email: {
      type: DataTypes.STRING(255),
      allowNull: false
    },
    tech_id: {
      type: DataTypes.INTEGER,
      allowNull: true
    },
    password: {
      type: DataTypes.STRING(255),
      allowNull: false
    },
    confirm_pass: {
      type: DataTypes.STRING(255),
      allowNull: false
    },
    mobile: {
      type: DataTypes.STRING(100),
      allowNull: false
    },
    created: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: Sequelize.Sequelize.fn('current_timestamp')
    },
    modified: {
      type: DataTypes.DATE,
      allowNull: true
    },
    role_id: {
      type: DataTypes.INTEGER,
      allowNull: false
    },
    fkey: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    latefee: {
      type: DataTypes.STRING(250),
      allowNull: true
    },
    attendenceupdate: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    appVersion: {
      type: DataTypes.INTEGER,
      allowNull: true,
      defaultValue: 0
    },
    examterm: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    otp: {
      type: DataTypes.INTEGER,
      allowNull: true
    },
    next_academic_year: {
      type: DataTypes.STRING(45),
      allowNull: true
    },
    previous_year: {
      type: DataTypes.STRING(45),
      allowNull: true
    },
    db: {
      type: DataTypes.STRING(100),
      allowNull: false
    },
    is_admin: {
      type: DataTypes.ENUM('Y','N'),
      allowNull: false,
      defaultValue: "N"
    },
    board: {
      type: DataTypes.STRING(100),
      allowNull: false
    },
    is_hostel: {
      type: DataTypes.ENUM('Y','N'),
      allowNull: false,
      defaultValue: "N"
    },
    is_transport: {
      type: DataTypes.ENUM('Y','N'),
      allowNull: false,
      defaultValue: "N"
    },
    is_payroll: {
      type: DataTypes.ENUM('Y','N'),
      allowNull: false,
      defaultValue: "N"
    },
    is_store: {
      type: DataTypes.ENUM('Y','N'),
      allowNull: false,
      defaultValue: "N"
    },
    is_monthlyfee: {
      type: DataTypes.ENUM('Y','N'),
      allowNull: true,
      defaultValue: "N"
    },
    is_prospectskip: {
      type: DataTypes.ENUM('Y','N'),
      allowNull: true,
      defaultValue: "N"
    },
    student_id: {
      type: DataTypes.INTEGER,
      allowNull: true
    },
    tc_template: {
      type: DataTypes.STRING(190),
      allowNull: true
    },
    state: {
      type: DataTypes.INTEGER,
      allowNull: true
    },
    city: {
      type: DataTypes.STRING(250),
      allowNull: true
    },
    franchise_db: {
      type: DataTypes.STRING(1000),
      allowNull: true
    },
    enroll_without_fees: {
      type: DataTypes.ENUM('0','1'),
      allowNull: true,
      defaultValue: "1"
    },
    install_date: {
      type: DataTypes.DATE,
      allowNull: true
    },
    last_login: {
      type: DataTypes.DATE,
      allowNull: true
    },
    token: {
      type: DataTypes.TEXT,
      allowNull: true
    },
    is_status: {
      type: DataTypes.ENUM('Y','N'),
      allowNull: true,
      defaultValue: "Y"
    },
    device_id: {
      type: DataTypes.STRING(45),
      allowNull: true
    }
  }, {
    sequelize,
    tableName: 'users',
    timestamps: false,
    freezeTableName: true,
    indexes: [
      {
        name: "PRIMARY",
        unique: true,
        using: "BTREE",
        fields: [
          { name: "id" },
        ]
      },
      {
        name: "id",
        unique: true,
        using: "BTREE",
        fields: [
          { name: "id" },
        ]
      },
    ]
  });
};
